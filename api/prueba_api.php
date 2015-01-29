<?php
if ($rparams["ID_USERGROUP"]==1){
/*	print "Entro <br/>";
	$qselect=$gdb->query("SELECT * FROM empleados;");	
	$qresults=$qselect->fetchAll(PDO::FETCH_ASSOC);
	foreach ($qresults as $qr_key => $result_field) {
		$qupdate=$gdb->prepare("UPDATE `empleados` SET `empleados`.`CUSUARIO`= :CUSUARIO WHERE ((`empleados`.`IDEMPLEADO`)= :IDEMPLEADO);");
		$qupdate->bindValue(':CUSUARIO', SaltedHASH($result_field["CUSUARIO"],":".$result_field["NUSUARIO"].":"), PDO::PARAM_STR);
		$qupdate->bindValue(':IDEMPLEADO', $result_field["IDEMPLEADO"], PDO::PARAM_INT);
		$qupdate->execute();
		$qupdate->debugDumpParams();
		print_r($qupdate->errorCode());
		print_r($qupdate->errorInfo());
		print SaltedHASH($result_field["CUSUARIO"],":".$result_field["NUSUARIO"].":").",";
	}
	print "Succes <br/>";
	/*
	print GetIPAddress();
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
		print  $key." : ".$_SERVER[$key]."<br/>";
    }*/
	/*$timeTarget = 0.05; // 50 milliseconds 

$cost = 10;
do {
    $start = microtime(true);
    print password_hash("test", PASSWORD_BCRYPT, ["cost" => $cost, "salt" => hash( 'sha512',':gruizfeliz:')]);
    $end = microtime(true);
	if (($end - $start) < $timeTarget){
	$cost++;
	}
} while (($end - $start) < $timeTarget);
echo " - ".hash( 'sha512',':gruizfeliz:')." -  Appropriate Cost Found: " . $cost . "\n";

/*	print "Entro <br/>";
	$qselect=$gdb->query("SELECT * FROM users;");	
	$qresults=$qselect->fetchAll(PDO::FETCH_ASSOC);
	foreach ($qresults as $qr_key => $result_field) {
		print password_hash("Avanzado-179", PASSWORD_BCRYPT, ["cost" => 9, "salt" => md5(":".$result_field["UserName"].":")])."<br/ >";
	}
	print "Succes <br/ >";
	*/
/*	print "Entro <br/>";
	$sendto="geraldruizfeliz@gmail.com";
	$subject="Prueba Trace V2";
	$email='
<html>
<head>
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
   <title>Prueba de Correo</title>
</head>
<body>
   <p>%saludo%</p>
</body>
</html>
';
	$bidings=Array ("%saludo%" => "Hola desde Trace");
	$headers = array();
	$headers[] = "MIME-Version: 1.0";
	$headers[] = "Content-type: text/html; charset=utf-8";
	$headers[] = "From: Gerald Ruiz <gruiz@gykluz.com>";
	$headers[] = "Reply-To: Gerald Ruiz <gruiz@gykluz.com>";
	$headers[] = "Subject: {$subject}";
	$headers[] = "X-Mailer: PHP/".phpversion();	

	TemplatedEmail($sendto, $subject, $email, $bidings, $headers);
	print "Succes <br/ >";*/
	//print mcrypt_create_iv(22, MCRYPT_DEV_URANDOM);
}else{
	http_response_code(403);
}
?>