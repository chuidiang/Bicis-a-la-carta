<?php
/* Javier Abellan, 16 Jun 2010.
 * Crea las tablas en base de datos si no existen y
 * luego redirige a la pagina de login.
 */
include ('include.php');
$link = mysql_connect ('localhost', $user, $password);
mysql_select_db($db);
crea_tabla_pieza($link);
crea_tabla_usuarios($link);
mysql_close($link);
header('Location:login.php');
?>