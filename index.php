<?php 
include ('include.php');
session_start();
en_sesion();
print_head($nombre_tienda.': Bicicletas a la carta');
?>
<body>
<div id="contenido">
<?php 



print_cabecera('Seleccione las piezas para su bicicleta'); 

if ($_SESSION['administrador']==true) {
	echo '<fieldset><legend>Administrador, puedes modificar:</legend>';
	echo '<ul><li><a href="admin_usuarios.php">usuarios</a></li>';
	echo '<li><a href="admin_piezas.php">piezas</a></li></ul></fieldset>';
}

$link = mysql_connect ('localhost', $user, $password);
mysql_select_db($db);

$precioTotal=0;



echo '<p>Vaya seleccionando las piezas que desea para la bici. Cuando termine, ';
echo 'pulse "Hacer pedido" para revisar el pedido e introducir sus datos de contacto</p>';
echo '<table>';
echo '<tr><th>Tipo pieza</th><th>Elija pieza</th><th>Ver</th></tr>';

for ($i=0; $i<count($tipo); $i++){

 	unset($piezas);
	$sql = "select id,nombre,precio,descripcion,foto from pieza where tipo=".$i;
	$result = mysql_query ($sql, $link);
	while ($row = mysql_fetch_array($result)) {
		$piezas[$row['id']]=$row['nombre'];
		$precios[$row['id']]=$row['precio'];
	}
       
	if ($_POST[$tipo[$i]] != null) {
		if ($_POST[$tipo[$i]] == -1){
			unset ($_SESSION[$tipo[$i]]);
		} else {
			$_SESSION[$tipo[$i]] = $_POST[$tipo[$i]];
		}
	}

	if ($_SESSION[$tipo[$i]] == null){
		$nombre="---";
		$precio=0;
	} else  {
		$nombre=$piezas[$_SESSION[$tipo[$i]]];
		$precio=$precios[$_SESSION[$tipo[$i]]];
	}
	$precioTotal+=$precio;

	if (substr($tipo[$i],0,1) == "-") {
		echo '<tr><td class="grupo" colspan="3">'.$texto_tipo[$tipo[$i]].'</td></tr>';
	} else {
	echo '<tr><td>'.$texto_tipo[$tipo[$i]].'</td><td>';

	echo '<form action="index.php" method="post">';
	echo '<div><select onchange="this.form.submit()" name="'.$tipo[$i].'">';
	echo '<option value="-1">---</option>';
	foreach ($piezas as $key => $value) 
	{ 
		if ($nombre==$value) {
			echo '<option value="'.$key.'" selected="selected">'.$value.'</option>';
		} else {
			echo '<option value="'.$key.'">'.$value.'</option>';
		}
	}
	echo '</select></div>';
	echo '</form></td>';
	echo '<td><a href="busca.php?tipo='.$i.'">Ver todas</a></td>';
	echo "</tr>";
	}
}
echo '</table>';
echo '<p></p>';
echo '<table class="total">';
echo '<tr><th>Base Imponible</th><th>IVA</th><th>Total</th></tr>';
echo '<tr><td class="euro">'.number_format($precioTotal,2).
	' &euro;</td><td class="euro">'.$iva.'</td><td class="euro">'.
	number_format($precioTotal*(1+$iva/100.0),2).' &euro;</td></tr>';
echo '</table>';
echo '<div id="pedir"><form method="post" action="pedir.php"><div><input type="submit" name="Pedir" value="Hacer pedido"/></div></form></div>';

for ($i=0; $i<count($tipo); $i++){
	if ($_POST[$tipo[$i]] != null) {
		if (($_POST[$tipo[$i]]==-1)){
			break;
		}
	$sql = 'select id,nombre,precio,descripcion,foto from pieza where id='.$_POST[$tipo[$i]];
	echo mysql_error();
	$result = mysql_query ($sql, $link);
	while ($row = mysql_fetch_array($result)) {
		echo "<h2>Recien seleccionado:</h2>";
		$foto=get_foto($row['nombre'],$row['foto']);
		echo '<img style="float:left;margin:10px;" width="100" height="100" src="'.$foto.'" alt="foto de pieza"/>';
		echo "<p>Nombre: ".$row['nombre']."</p>";
		echo "<p>Descripcion: ".$row['descripcion']."</p>";
		echo "<p>Precio: ".$row['precio']." &euro;</p>";
		echo '<p style="clear:both;"></p>';
	}
}}
mysql_close($link);
?>
</div>
</body>
</html>
