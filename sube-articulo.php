<?php

$nombre_archivo = $_FILES['foto']['name'];
$directorio_definitivo = $_SERVER['DOCUMENT_ROOT'].'/bicis/';
move_uploaded_file($_FILES['foto']['tmp_name'], $directorio_definitivo.$nombre_archivo);

include ("include.php");
$link = mysql_connect ('localhost', $user, $password);
mysql_select_db($db);
$sql = "insert into pieza (nombre,descripcion,precio,tipo,foto) values ('".
	htmlentities($_POST['nombre'],ENT_QUOTES, 'utf-8')."','".nl2br(htmlentities($_POST['descripcion'],ENT_QUOTES, 'utf-8'))."',".
	$_POST['precio'].",".$_POST['tipo'].",'".$nombre_archivo."')";
mysql_query ($sql, $link);
echo mysql_error();
mysql_close ($link);
header( 'Location: admin_piezas.php' ) ;
?>
