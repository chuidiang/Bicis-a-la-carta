<?php 
include ('include.php');
session_start();
en_sesion();
if ($_SESSION['administrador'] != true) {
	header ('Location:index.php');
}

print_head($nombre_tienda.': administracion de usuarios');
?>
<body>
<div id="contenido">
<?php print_cabecera('Administracion de usuarios');

$link = mysql_connect ('localhost', $user, $password);
mysql_select_db($db);

if (isset($_POST['crear'])) {
	if ($_POST['password'] != $_POST['repassword']) {
		echo '<p class="error">Las passwords no coinciden</p>';
	} else
	{
		$_POST['nombre']=escapa_comillas($_POST['nombre']);
		$_POST['email']=escapa_comillas($_POST['email']);
		$_POST['telefono']=escapa_comillas($_POST['telefono']);
		$_POST['fecha']=escapa_comillas($_POST['fecha']);
		$sql = "insert into usuarios (nombre, password, email, telefono, fecha_caducidad, administrador ) ".
			"values ('".$_POST['nombre']."','".md5($_POST['password'])."','".$_POST['email']."','".
			$_POST['telefono']."','".$_POST['fecha']."', 0)";

		mysql_query($sql);
		if (mysql_error()){
			echo '<p class="error">Posiblemente alg&uacute;n dato es err&oacute;neo '.mysql_error().'</p>';
		}
		unset($_POST['nombre']);
		unset($_POST['email']);
		unset($_POST['telefono']);
		unset($_POST['id_usuario']);
		unset($_POST['fecha']);
	}
}

if (isset($_POST['borrar'])) {
	$sql = "delete from usuarios where id_usuario=".$_POST['id_usuario'];
	mysql_query($sql);
}

if (isset($_POST['editar'])) {
	$sql = "select * from usuarios where id_usuario=".$_POST['id_usuario'];
	$result = mysql_query($sql);
	while ($row = mysql_fetch_array($result)) {
		$_POST['nombre']=$row['nombre'];
		$_POST['email']=$row['email'];
		$_POST['telefono']=$row['telefono'];
		$_POST['id_usuario']=$row['id_usuario'];
		$_POST['fecha']=$row['fecha_caducidad'];
	}
}



if (isset($_POST['update'])) {
	if ($_POST['password']!='') {
		if ($_POST['password'] == $_POST['repassword']) {
			$cambio_password=", password='".md5($_POST['password'])."'";
		} else {
			echo '<p class="error">Las passwords no coinciden</p>';
		}
	}
	$_POST['nombre']=escapa_comillas($_POST['nombre']);
	$_POST['email']=escapa_comillas($_POST['email']);
	$_POST['telefono']=escapa_comillas($_POST['telefono']);
	$_POST['fecha']=escapa_comillas($_POST['fecha']);
	
	$sql="update usuarios set nombre='".$_POST['nombre']."', email='".$_POST['email']."', telefono='".
		$_POST['telefono']."', fecha_caducidad='".$_POST['fecha']."'";
	$sql .= $cambio_password;
	$sql .= " where id_usuario=".$_POST['id_usuario'];
	mysql_query($sql);
	if (mysql_error()){
		echo '<p class="error">Error al modificar usuario '.mysql_error().'</p>';
	}
}

if (isset($_POST['cancelar'])  || isset($_POST['update'])) {
	unset($_POST['nombre']);
	unset($_POST['email']);
	unset($_POST['telefono']);
	unset($_POST['id_usuario']);
	unset($_POST['fecha']);
}

?>
<form method="post" action="admin_usuarios.php">
<?php if (isset($_POST['editar'])) {
	echo '<fieldset><legend>Modificar usuario</legend>';
} else {
   echo '<fieldset><legend>Crear nuevo usuario</legend>';
} ?>
<p><label>Nombre de usuario: </label><input type="text" name="nombre" value="<?php echo $_POST['nombre']; ?>"></input></p>
<?php if (isset($_POST['editar'])) {
	echo '<p>Deje las password en blanco si no quiere modificarlas</p>';
} ?>
<p><label>Password : </label><input type="password" name="password"></input>
<label>Reintroduzca password : </label><input type="password" name="repassword"></input></p>
<p><label>email : </label><input type="text" name="email" value="<?php echo $_POST['email']; ?>"></input>
<label>telefono : </label><input type="text" name="telefono" value="<?php echo $_POST['telefono']; ?>"></input></p>
<p><label>V&aacute;lido hasta (formato yyyy-mm-dd) : </label>
<input type="text" name="fecha" value="<?php if (isset($_POST['fecha'])) {
   echo $_POST['fecha']; 
} else {
	echo date('Y-n-j',time()+3600*24*7);
} ?>"></input></p>
<p>
<?php
	if (isset($_POST['editar'])) {		
?>
	<input type="hidden" name="id_usuario" value="<?php echo $_POST['id_usuario']; ?>"></input>
	<input type="submit" name="update" value="Modificar"></input>
	<input type="submit" name="cancelar" value="Cancelar"></input>
<?php } else { ?>		
	<input type="submit" name="crear" value="Crear"></input>
<?php } ?>
</p>
</fieldset>
</form>
<p></p>
<table><thead>
<tr><th>nombre</th><th>e-mail</th><th>telefono</th><th>v&aacute;lido hasta</th>
<th colspan="2">acciones</th>
</tr>
</thead>
<?php

$sql = "select * from usuarios";
$result = mysql_query ($sql, $link);
while ($row = mysql_fetch_array($result)) {
	echo '<tr>';
	echo '<td>'.$row['nombre'].'</td>';
	echo '<td>'.$row['email'].'</td>';
	echo '<td>'.$row['telefono'].'</td>';
	if ($row['fecha_caducidad'] < date('Y-m-d',time())) {
		echo '<td class="caducado">'.$row['fecha_caducidad'].'</td>';
	} else {
		echo '<td>'.$row['fecha_caducidad'].'</td>';
	}
	echo '<td>';
	if ($row['administrador'] != 1) {
		echo '<form action="admin_usuarios.php" method="post">';
		echo '<input type="hidden" name="id_usuario" value="'.$row['id_usuario'].'"></input>';
		echo '<input type="submit" name="borrar" value="Borrar" onclick="return confirm(\'Seguro que desea borrar el usuario\')"></input></form>';
	}
	echo '</td>';
	echo '<td><form action="admin_usuarios.php" method="post">';
	echo '<input type="hidden" name="id_usuario" value="'.$row['id_usuario'].'"></input>';
	echo '<input type="submit" name="editar" value="Editar"></input></form></td>';
	echo '</tr>';
}
echo '</table>';
mysql_close();
?>
</div>
</body>
</html>
