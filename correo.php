<?php 
include ('include.php');
session_start();
en_sesion();

$mensaje = '<html><head></head><body>';
$mensaje .= '<p>Pedido realizado por : '.$_POST['nombre'].'</p>';
$mensaje .= '<p>e-mail : '.$_POST['email'].'</p>';
$mensaje .= '<p>telefono : '.$_POST['telefono'].'</p>';
$mensaje .= '<table style="border-collapse:collapse; border:solid 1px black; width=80%;" >';
$mensaje .= '<tr><th>Tipo pieza</th><th>nombre</th></tr>';

$link = mysql_connect ('localhost', $user, $password);
mysql_select_db($db);

$precioTotal=0;

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
	$mensaje .= '<tr><td style="border:solid 1px black;">'.$tipo[$i].'</td>';
        $mensaje .= '<td style="border:solid 1px black;">'.$nombre.'</td>';
	$mensaje .= "</tr>";
}
$mensaje .= '</table><table>';
$mensaje .= '<tr><th>B. Imponible</th><th>IVA</th><th>Total</th></tr>';
$mensaje .= '<tr><td style="border:solid 1px black;">'.number_format($precioTotal,2).'</td>';
$mensaje .= '<td style="border:solid 1px black;">'.$iva.'</td>';
$mensaje .= '<td style="text-align:right; ; border:solid 1px black;">'.number_format($precioTotal*(1+$iva/100.0),2).'</td></tr>';
$mensaje .= '</table>';
$mensaje .= '</body></html>';
$mensaje = wordwrap($mensaje,70);

mysql_close($link);

// Always set content-type when sending HTML email
$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-type:text/html;charset=iso-8859-1\r\n";

// More headers
$headers .= 'From: <'.$email_tienda.">\r\n";
$headers .= 'Cc: <'.$_POST['email'].">\r\n";
$enviado = mail ( $email_tienda , 'Pedido bicicleta' , 
   $mensaje, 
   $headers );

print_head($nombre_tienda.': Correo con pedido enviado');
?>
<html>
<head>
<title>Pedido enviado</title>
<link rel=StyleSheet href="style.css" type="text/css"/>
</head>
<body>
<?php 
print_head('Pedido enviado');
?>
<body>
<div id="contenido">
<?php
print_cabecera('Pedido enviado');
if ($enviado) {
?>
<p>El correo se ha enviado con &eacute;xito. Si has puesto tu direcci&oacute;n de correo, recibir&aacute;s una copia</p>
<p>En breve nos pondremos en contacto contigo para comentar o confirmar el pedido</p>
<p>Gracias por la confianza depositada en nosotros</p>
<?php } else { ?>
<p class="error">Ha habido alg&uacute;n problema en el env&iacute;o del correo.</p>
<p>No cierres el navegador para no perder tu selecci&oacute;n de piezas, vuelve a la <a href="index.php">p&aacute;gina inicial</a> y vuelve a intentarlo m&aacute; tarde.</p>
<?php } ?>
</div>
</body>
</html>
