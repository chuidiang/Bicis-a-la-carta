<?php
include ('include.php');
session_start();
en_sesion();
print_head($nombre_tienda.': Selecci&oacute;n de piezas');
?>
<body>
<div id="contenido"><?php
$indice = $_GET['tipo'];
print_cabecera('Selecci&oacute;n/B&uacute;squeda de '.$texto_tipo[$tipo[$indice]]);
?>
<div class="filtro"><?php
echo '<form method="post" action="busca.php?tipo='.$indice.'">';
?>
<fieldset><legend>Filtro</legend>
<p>Escriba s&oacute;lo en los campos por los que quiere filtrar y pulse
"Filtrar". Deje todos los campos vac&iacute;os y pulse "Filtrar" para
ver todas las piezas</p>
<label>Precio menor que </label> <input type="text" name="filtro_precio" />
<label>Descripci&oacute;n contiene (s&oacute;lo una palabra)</label> <input
	type="text" name="filtro_descripcion" /> <input type="submit"
	value="Filtrar" /></fieldset>
</form>
</div>
<p></p>
<?php
$link = mysql_connect ('localhost', $user, $password);
mysql_select_db($db);
$sql = "select id,nombre,foto,descripcion,precio from pieza where tipo=".$indice;
if (($_POST['filtro_precio'] != "") && ($_POST['filtro_precio'] != null)){
	$sql .= " and precio < ".$_POST['filtro_precio'];
}
if (($_POST['filtro_descripcion'] != "") && ($_POST['filtro_descripcion'] != null)){
	$sql .= " and upper(descripcion) like '%".strtoupper($_POST['filtro_descripcion'])."%'";
}
$result = mysql_query ($sql, $link);
echo '<h2>Selecci&oacute;n '.$texto_tipo[$tipo[$indice]].'</h2>';
echo '<table>';
echo '<tr><th></th><th>nombre</th><th>foto</th><th>descripci&oacute;n</th><th>precio</th></tr>';
while ($row = mysql_fetch_array($result)) {
	echo '<tr>';
	echo '<td>';
	echo '<form method="post" action="index.php"><input type="hidden" name="'.$tipo[$indice].'" value="'.$row['id'].'"/>';
	echo '<input type="submit" value="Seleccionar"/></form>';
	echo '</td>';
	echo '<td>';
	echo $row['nombre'];
	echo '</td>';
	echo '<td>';
	$foto=get_foto($row['nombre'],$row['foto']);
	echo '<img src="'.$foto.'" width="100" height="100"/>';
	echo '</td>';
	echo '<td>';
	echo $row['descripcion'];
	echo '</td>';
	echo '<td class="euro">';
	echo number_format($row['precio'],2);
	echo '</td>';
	echo '</tr>';
}
mysql_close($link);
?></div>
</body>
</html>
