<?php

include ('config.php');

/* Tipos de piezas. Las que comienzan con - se consideran separadores en la presentacion.
 * Los tipos apareceran en el mismo orden en las tablas visibles al usuario.
 */
$tipo = array('cuadro','horquilla','-montaje','jgo_direccion','potencia','punos','cable','manillar',
	'tija_sillin','sillin','cierre_sillin','-ruedas','cubiertas', 'camaras', 'llantas', 'radios',
	'buje_delantero','buje_trasero', '-grupo', 'mandos_cambio', 'freno_delantero', 'freno_trasero', 
	'cadena','casete','jgo_bielas', 'desviador', 'cambio_trasero', 'pedales', '-otro', 'extras' ); 

/* Texto visibles para los tipos de piezas de $tipo */
$texto_tipo = array(
	'cuadro'=>'Cuadro',
		'horquilla'=>'Horquilla',
		'-montaje'=>'MONTAJE',
		'jgo_direccion'=>'Jgo. Direcci&oacute;n',
		'potencia'=>'Potencia',
		'punos'=>'Pu&ntilde;os',
		'cable'=>'Cable',
		'manillar'=>'Manillar',
		'tija_sillin'=>'Tija sill&iacute;n',
		'sillin'=>'Sill&iacute;n',
		'cierre_sillin'=>'Cierre sill&iacute;n',
		'-ruedas'=>'RUEDAS',
		'cubiertas'=>'Cubiertas',
		'camaras'=>'C&aacute;maras',
		'llantas'=>'Llantas',
	       	'radios'=>'Radios',
		'buje_delantero'=>'Buje delantero',
		'buje_trasero'=>'Buje trasero',
		'-grupo'=>'GRUPO',
		'mandos_cambio'=>'Mandos cambio', 
		'freno_delantero'=>'Freno delantero',
	       	'freno_trasero'=>'Freno trasero', 
		'cadena'=>'Cadena',
		'casete'=>'Casete',
		'jgo_bielas'=>'Jgo. bielas',
		'desviador'=>'Desviador', 
		'cambio_trasero'=>'Cambio trasero',
		'pedales'=>'Pedales',
		'-otro'=>'EXTRAS',
	       	'extras'=>'extras'
);

/* Escribe un div con el logo de la tienda y enlace a la url principal,
 * segun variables definidas en config.php */
function print_cabecera($titulo) {
	global $url_tienda, $logo_tienda, $nombre_tienda;
	echo '<div id="cabecera">';
	echo '<a href="'.$url_tienda.'"><img src="'.$logo_tienda.'" alt="logo '.$nombre_tienda.'"/></a>';
	echo '<h1>'.$titulo.'</h1>';
	echo '</div>';
}

/* Escribe los tag html de html y head completos */
function print_head($titulo) {
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"';
	echo '"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
	echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es">';
	echo '<head>';
	echo '<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>';
	echo '<title>'.$titulo.'</title>';
	echo '<link rel="StyleSheet" href="style.css" type="text/css"/>';
	echo '</head>';
}

/* Devuelve el fichero de foto para una pieza dada.
 * Si existe un fichero jpg cuyo nombre coincida con el codigo de la pieza,
 * devuelve ese fichero.
 * Si no existe ese fichero, pero este seteado el segundo parametro foto que
 * se le pasa, devuelve ese segundo parametro. Ese segundo parametro deberia ser
 * el resultado de la consulta a base de datos.
 * En caso de que no haya nada, devuelve sinfoto.jpg, que es la foto de "imagen
 * no disponible".
 */
function get_foto ($nombre, $foto) {
	if (file_exists($nombre.".jpg")) {
		return $nombre.".jpg";
	}
	if (!isset($foto) || $foto=="") {
		return "sinfoto.jpg";
	}
	return $foto;
}

/* Crea la tabla de usuarios si no esta creada y le anade un usuario
 * de administracion por defecto, segun las variables definidas en
 * config.php
 */
function crea_tabla_usuarios ($link) {
	global $user_admin, $passwd_admin, $email_tienda, $telefono_tienda;
	$result = mysql_query("show tables like 'usuarios'");
	if(mysql_fetch_row($result) == false) {
		$sql = 'create table if not exists usuarios (id_usuario smallint auto_increment primary key, '.
			'nombre varchar(60), email varchar(60), telefono varchar (60), '.
			'fecha_caducidad date, administrador tinyint(1), password varchar(32))';
		$resultado = mysql_query($sql, $link);
		$sql = "insert into usuarios (nombre, password, email, telefono, fecha_caducidad, administrador) values ".
			"('".$user_admin."', '".md5($passwd_admin)."','".$email_tienda."','".$telefono_tienda."','2100-12-31',1)";
		mysql_query($sql, $link);
	}
}

/* Crea en base de datos la tabla pieza si no existe */
function crea_tabla_pieza ($link) {
	$sql = 'create table if not exists pieza (id smallint auto_increment primary key, '.
			'nombre varchar(60) unique, descripcion varchar(256), precio decimal (10,2), '.
			'tipo smallint, foto varchar(256))';
	$resultado = mysql_query($sql, $link);
}

/* Crea en base de datos la talba regla si no existe */
function crea_tabla_reglas ($link) {
	$sql = 'create table if not exists regla (id smallint auto_increment primary key, '.
	       'nombre varchar(60) unique, tipo smallint)';
	$resultado = mysql_query($sql, $link);
}

/* Redirige a la pagina de login si no estamos en sesion */
function en_sesion () {
	if ($_SESSION['sesion'] != true) {
		header('Location:login.php');
	}
}

/* Escapa las comillas y cambia los caracteres html */
function escapa_comillas ($cadena){
	if (!get_magic_quotes_gpc()) {
		return addslashes($cadena);
	} else {
		return $cadena;
	}
}

/* Quita escapes a las comillas */
function quita_escapa_comillas ($cadena){
	return stripslashes ($cadena);
}

?>
