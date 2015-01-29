<?php
//error_reporting(0);

$emailnotifications="Notificaciones <notificaciones@tracesolar.com>";

$dbhost='127.0.0.1';
$dbusuario='usuarios';
$dbpassword='abcd.1234';
$dbname='trace';
try {
	$gdb = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbusuario, $dbpassword);
} catch (PDOException $e) {
	print "<br>Error!: ".$e->getMessage()."<br/>";
	die();
}
?>