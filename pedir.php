<?php 
include ('include.php');
session_start();
en_sesion();
print_head($nombre_tienda.': Datos de contacto');
?>
<body>
<div id="contenido">
<?php print_cabecera('Datos de contacto'); ?>
<p>Revise si esto es lo que ha pedido, verifique sus datos de contacto y pulse "Enviar".
Se enviar&aacute; un correo a <?php echo $nombre_tienda; ?> y usted recibir&aacute; una copia 
en el e-mail que aparece abajo.</p>
<?php

$link = mysql_connect ('localhost', $user, $password);
mysql_select_db($db);

$precioTotal=0;

echo '<table>';
echo '<tr><th>Tipo pieza</th><th>nombre</th></tr>';

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

	/* Ver si el elemento esta pedido */
	if ($_SESSION[$tipo[$i]] == null){
		$nombre="---";
		$precio=0;
	} else  {
		$nombre=$piezas[$_SESSION[$tipo[$i]]];
		$precio=$precios[$_SESSION[$tipo[$i]]];
	}
	$precioTotal+=$precio;

	if (substr($tipo[$i],0,1)=="-" ){
		echo '<tr><td class="grupo" colspan="2">'.$texto_tipo[$tipo[$i]].'</td></tr>';
	} else {
	echo '<tr><td>'.$texto_tipo[$tipo[$i]].'</td>';
        echo '<td>'.$nombre.'</td>';
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

$sql = "select * from usuarios where id_usuario=".$_SESSION['id_usuario'];
$result=mysql_query($sql);
while ($row = mysql_fetch_array($result)) {
	$nombre_usuario=$row['nombre'];
	$email=$row['email'];
	$telefono=$row['telefono'];
}


mysql_close($link);
?>
<p style="clear:both;"></p>

<form action="correo.php" method="post">
<fieldset><legend>Datos personales</legend>
<label>Nombre:</label>
<input type="text" name="nombre" value="<?php echo $nombre_usuario; ?>" readonly/>
<label>e-mail:</label>
<input type="text" name="email" value="<?php echo $email; ?>"/>
<label>Telefono:</label>
<input type="text" name="telefono" value="<?php echo $telefono; ?>"/>
<input type="submit" value="Enviar"/>
</fieldset>
</form>
</body>
</div>
</html>
