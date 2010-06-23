<?php
include ('include.php');
session_start();
unset ($_SESSION['sesion']);
unset ($_SESSION['administrador']);
unset ($_SESSION['id_usuario']);


$link = mysql_connect ('localhost', $user, $password);
mysql_select_db($db);
crea_tabla_usuarios($link);

if ($_POST['usuario']){


	$sql = "select id_usuario, password, administrador, fecha_caducidad from usuarios where nombre='".$_POST['usuario']."'";
	$result = mysql_query ($sql, $link);
	$motivo='<p class="error">El usuario/contrase&ntilde;a no son v&aacute;lidos</p>';
	while ($row = mysql_fetch_array($result)) {
		if ($row['password']==md5($_POST['password'])) {
			$_SESSION['sesion']=true;
			$_SESSION['id_usuario']=$row['id_usuario'];
			if ($row['administrador'] == 1) {
				$_SESSION['administrador']=true;
			}
			if ($row['fecha_caducidad'] < date('Y-m-d',time())) {
				$motivo='<p class="error">Su cuenta ha caducado. P&oacute;ngase en'.
					' contacto con '.$nombre_tienda.' para que se la vuelvan a activar</p>';
			} else {
				header('Location:index.php');
			}
		}
	}
}


mysql_close($link);

print_head($nombre_tienda.': Login');
?>
<body>
<div id="contenido"><?php print_cabecera('Introduzca usuario y password'); 
if ($motivo) {
	echo $motivo;
}
?>
<form action="login.php" method="post">
<div><label>Usuario:</label> <input type="text" name="usuario"></input>
<label>Password :</label>
</td>
<input type="password" name="password"></input> <input type="submit"
	value="Entrar"></input></div>
</form>

</body>
</html>
