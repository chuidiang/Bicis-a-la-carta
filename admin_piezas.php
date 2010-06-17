<?php 
include ('include.php');
session_start();
en_sesion();
if ($_SESSION['administrador'] != true) {
	header ('Location:index.php');
}

$link = mysql_connect ('localhost', $user, $password);
mysql_select_db($db);

if (isset($_POST['cambiar'])) {
	$sql = "update pieza set tipo=".$_POST['id_pieza']." where nombre like '".$_POST['comienzo']."%'";
	mysql_query($sql);
	$error = mysql_error();
} 



print_head($nombre_tienda.': administracion de piezas');

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
		echo mysql_error();
	}
	fclose($fichero);
}

?>
<body>
<div id="contenido">
<?php print_cabecera('Administracion de piezas');
if ($error) {
	echo '<p class="error">'.$error.'</p>';
}
?>

<form method="post">
<fieldset><legend>Determinar de que tipo es cada pieza</legend>
<label>Todas las piezas cuyo c&oacute;digo comienza con</label>
<input type="text" name="comienzo"></input>
<label>son </label>
<select name="id_pieza">
<?php
for ($i=0; $i<count($tipo); $i++){
	if (substr($tipo[$i],0,1) != "-") {
		echo '<option value="'.$i.'">'.$texto_tipo[$tipo[$i]].'</option>';
	}
}
?>
<input type="submit" action="admin_piezas" value="cambiar" name="cambiar"></input>
</select>
</fieldset>
</form>
<p></p>
<form method="post" enctype="multipart//form-data">
<fieldset>
<legend>Importar un fichero de piezas</legend>
<p>El fichero de piezas debe ser un fichero en formato CSV con tres columnas: c&oacute;digo de la pieza,
descripci&oacute;n y precio de venta al p&uacute;blico sin IVA. El formato CSV puedes conseguirlo salvando una
tabla Excel como fichero .csv</p>
<p>La primera l&iacute;nea no debe ser de cabecera, es decir, no puede ser</p>
<p><strong>"CODIGO";"DESCRIPCION";"PVP"</strong></p><p>Cada l&iacute;nea del fichero contiene una de las piezas 
con los tres campos/columnas indicados. Los campos deben estar encerrados entre comillas,
 el separador ser un punto y coma y el precio
ser s&oacute;lo un n&uacute;mero, usando una coma para serparar los decimales y sin s&iacute;mbolo de moneda. Es decir, 
cada l&iacute;nea del fichero debe ser algo as&iacute;</p>
<p><strong>"FC7800CDX29N";"BIELAS DURA ACE CARBONO 172,5MM 52/39";649,00</strong></p>
<p>No es correcto</p><p><strong>"FC7800CDX29N";"BIELAS DURA ACE CARBONO 172,5MM 52/39";<strong class="error">1.649,00 &euro;
</strong></strong></p><p> porque el precio lleva un punto como separador de miles y el s&iacute;mbolo de euro</p>
<label>Seleccione un fichero CSV para importar </label><input name="fichero_csv" type="file"></input>
<input type="submit" name="subir" value="Importar"></input>
</fieldset>
</form>
<?php
mysql_close();
?>
</div>
</body>
</html>
