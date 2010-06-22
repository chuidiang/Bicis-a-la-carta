<?php
include ('include.php');
session_start();
en_sesion();
if ($_SESSION['administrador'] != true) {
	header ('Location:index.php');
}

$link = mysql_connect ('localhost', $user, $password);
mysql_select_db($db);

/* Cambiar piezas de tipo se se ha pedido asi */
if (isset($_POST['cambiar'])) {
	/* Hacer los cambios */
	$sql = "update pieza set tipo=".$_POST['id_pieza']." where nombre like '".$_POST['comienzo']."%'";
	mysql_query($sql);
	$error=mysql_error();
	
	/* guardar la regla en bd */
	$sql = "insert into regla (nombre, tipo) values ('".$_POST['comienzo']."',".$_POST['id_pieza'].")".
	 " on duplicate key update tipo=".$_POST['id_pieza'];
	mysql_query($sql);
}

/* Importar un fichero de piezas csv */
if (isset($_POST['subir'])){
	$nombre_archivo = $_FILES['fichero_csv']['tmp_name'];
	echo $_FILES['fichero_csv']['tmp_name'];
	$fichero = fopen($nombre_archivo, "r");
	while (( $data = fgetcsv ( $fichero , 300 , ";" )) !== false ) {
		if (count($data) != 3) {
			next;
		}
		$codigo = $data[0];
		$descripcion = $data[1];
		$precio = str_replace(",", ".", $data[2]);

		$sql = "insert into pieza (nombre, descripcion, precio) values ('".$codigo."', '".$descripcion."',".$precio.")".
			" on duplicate key update descripcion='".$descripcion."', precio=".$precio;
		$result = mysql_query($sql);
		$error=mysql_error();
	}
	fclose($fichero);
	
	/* Aplicar las reglas a las piezas recien importadas */
	$sql = 'select * from regla';
	$result = mysql_query($sql);
	while ($row = mysql_fetch_array($result)) {
		$sql2 = "update pieza set tipo=".$row['tipo']." where nombre like '".$row['nombre']."%' and tipo is null";
		mysql_query($sql2);
	}
}


print_head($nombre_tienda.': administracion de piezas');


?>
<body>
<div id="contenido"><?php print_cabecera('Administracion de piezas');
if ($error) {
	echo '<p class="error">'.$error.'</p>';
}
?>

<form method="post">
<fieldset><legend>Reglas para asignar tipos de piezas</legend>
<?php
   $sql = "select * from regla";
   $result = mysql_query($sql); 
   while ($row = mysql_fetch_array($result)){
      echo '<p>Las c&oacute;digos que empiezan por <strong>'.$row['nombre'].'</strong> son <strong>'.
      $texto_tipo[$tipo[$row['tipo']]].'</strong></p>';
   }
?> 
<label>Nueva regla:<br/>Los c&oacute;digos que empiezan por </label> <input type="text"
	name="comienzo"></input> <label>son </label> <select name="id_pieza">
	<?php
	for ($i=0; $i<count($tipo); $i++){
		if (substr($tipo[$i],0,1) != "-") {
			echo '<option value="'.$i.'">'.$texto_tipo[$tipo[$i]].'</option>';
		}
	}
	?>
	<input type="submit" action="admin_piezas" value="cambiar"
		name="cambiar"></input>
</select>
<?php
   $sql="select nombre, descripcion from pieza where tipo is null limit 10";
   $result = mysql_query($sql);
   while ($row = mysql_fetch_array($result)){
      if (!isset($primera_fila)) {
      	$primera_fila=true;
      	echo '<p>Algunas piezas todav&iacute;a sin asignar:<br/>';
      }
      echo '<strong>'.$row['nombre'].'</strong> --- '.$row['descripcion'].'<br/>';
   }
   if (isset($primera_fila)){
   	echo '</p>';
   }
?>
</fieldset>
</form>
<p></p>
<form action="sube-articulo.php" method="post" enctype="multipart/form-data">
<fieldset><legend>Subir pieza</legend>
<div><label>Tipo de pieza:</label>
<select name="tipo">
<?php
for ($i=0; $i<count($tipo); $i++){
	if (substr($tipo[$i],0,1) == "-"){
		continue;
	}
	echo '<option value="'.$i.'">'.$texto_tipo[$tipo[$i]].'</option>';
}
?>
</select><br/>
<label>Nombre :</label>
<input type="text" name="nombre"></input><br/>
<label>Descripcion :</label>
<textarea name="descripcion"></textarea><br/>
<label>Precio (Euros):</label>
<input type="text" name="precio"></input><br/>
<label>Foto:</label>
<input type="file" name="foto"></input><br/>
<input type="submit" value="Guardar"/>
</div>
</fieldset>
</form>
<p></p>
<form method="post" enctype="multipart/form-data">
<fieldset><legend>Importar un fichero de piezas</legend>
<p>El fichero de piezas debe ser un fichero en formato CSV con tres
columnas: c&oacute;digo de la pieza, descripci&oacute;n y precio de
venta al p&uacute;blico sin IVA. El formato CSV puedes conseguirlo
salvando una tabla Excel como fichero .csv</p>
<p>La primera l&iacute;nea no debe ser de cabecera, es decir, no puede
ser</p>
<p><strong>"CODIGO";"DESCRIPCION";"PVP"</strong></p>
<p>Cada l&iacute;nea del fichero contiene una de las piezas con los tres
campos/columnas indicados. Los campos deben estar encerrados entre
comillas, el separador ser un punto y coma y el precio ser s&oacute;lo
un n&uacute;mero, usando una coma para serparar los decimales y sin
s&iacute;mbolo de moneda. Es decir, cada l&iacute;nea del fichero debe
ser algo as&iacute;</p>
<p><strong>"FC7800CDX29N";"BIELAS DURA ACE CARBONO 172,5MM 52/39";649,00</strong></p>
<p>No es correcto</p>
<p><strong>"FC7800CDX29N";"BIELAS DURA ACE CARBONO 172,5MM 52/39";<strong
	class="error">1.649,00 &euro; </strong></strong></p>
<p>porque el precio lleva un punto como separador de miles y el
s&iacute;mbolo de euro</p>
<label>Seleccione un fichero CSV para importar </label><input
	name="fichero_csv" type="file"></input> <input type="submit"
	name="subir" value="Importar"></input></fieldset>
</form>
	<?php
	mysql_close();
	?></div>
</body>
</html>
