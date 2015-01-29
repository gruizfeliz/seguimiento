<?php
if ($api2==2){
	if ($action=="1"){
		$rparams["api"]="21";
		$api=2;
		$api2=1;
		$squote="";
		$sqwhere="";
		$last_log=LastLog($rparams["ID_EMPLEADO"],$gdb);
		$qselect=$gdb->prepare("SELECT COUNT(*) AS `count` FROM `tareas` WHERE (((`IDEMPLEADO`)=:IDEMPLEADO) AND ((`IDESTATUS`)<3) AND ((`FECHAENTREGA`)<NOW()));");
		$qselect->bindValue(':IDEMPLEADO', $rparams["ID_EMPLEADO"], PDO::PARAM_STR);
		$qselect->execute();
		$qresults=$qselect->fetchAll(PDO::FETCH_ASSOC);
		//$qselect->debugDumpParams();
		//print_r($qselect->errorCode());
		//print_r($qselect->errorInfo());
		//if ((count($qresults)>0) && ($qresults[0]["count"]>0)){
		$rresponse["results"][]=Array("M" => "Tiene tareas vencidas",
										"C" => (count($qresults)>0) ? ($qresults[0]["count"]+0) : 0,
										"A" => "t_vencidas");
		//}
		$qselect=$gdb->prepare("SELECT COUNT(*) AS `count` FROM `tareas` WHERE (((`IDEMPLEADO`)=:IDEMPLEADO) AND ((`IDESTATUS`)<3) AND ((`FECHAENTREGA`)>NOW()) AND ((ADDDATE(`FECHAENTREGA`, -`RECORDATORIO`))<=NOW()));");
		$qselect->bindValue(':IDEMPLEADO', $rparams["ID_EMPLEADO"], PDO::PARAM_STR);
		$qselect->execute();
		$qresults=$qselect->fetchAll(PDO::FETCH_ASSOC);
		//$qselect->debugDumpParams();
		//print_r($qselect->errorCode());
		//print_r($qselect->errorInfo());
		$rresponse["results"][]=Array("M" => "Tiene tareas por vencer",
										"C" => (count($qresults)>0) ? ($qresults[0]["count"]+0) : 0,
										"A" => "t_porvencer");
		$qselect=$gdb->prepare("SELECT COUNT(*) AS `count` FROM `tareas` WHERE (((`IDEMPLEADO`)=:IDEMPLEADO) AND ((`IDESTATUS`)<3) AND ((`FECHA`)>='".$last_log."'));");
		$qselect->bindValue(':IDEMPLEADO', $rparams["ID_EMPLEADO"], PDO::PARAM_STR);
		$qselect->execute();
		//$qselect->debugDumpParams();
		//print_r($qselect->errorCode());
		//print_r($qselect->errorInfo());
		$qresults=$qselect->fetchAll(PDO::FETCH_ASSOC);
		$rresponse["results"][]=Array("M" => "Tiene tareas nuevas",
										"C" => (count($qresults)>0) ? ($qresults[0]["count"]+0) : 0,
										"A" => "t_nuevas");
		$rresponse["status"]="Success";
		$rresponse["results"]=Utf8EncodeRecursive($rresponse["results"]);
		print json_encode($rresponse);

		$qselect=$gdb->prepare("SELECT `ID` FROM `log_requests` WHERE (((`IDEMPLEADO`)= :IDEMPLEADO) AND ((`when`)>=CURDATE()) AND ((`T`)='notifications'));");
		$qselect->bindValue(':IDEMPLEADO', $rparams["ID_EMPLEADO"], PDO::PARAM_INT);
		$qselect->execute();
		$qresults=$qselect->fetchAll(PDO::FETCH_ASSOC);
		if (count($qresults)<=1){
			$sendto=GetUserEmail($rparams["ID_EMPLEADO"], $gdb);
			$subject="Seguimiento Notificaciones";
			$email='
<html>
<head>
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
   <title>'.$subject.'</title>
</head>
<body>
   <p>%t_vencidas%</p>
   <p>%t_porvencer%</p>
   <p>%t_nuevas%</p>
   
   <p>Mensaje automatico no responder.</p>
</body>
</html>
';
			$bidings=Array ("%t_vencidas%" => (($rresponse["results"][0]["C"]>0)? "Tiene <strong>".$rresponse["results"][0]["C"]."</strong>" : "No tiene")." tareas vencidas.",
							"%t_porvencer%" => (($rresponse["results"][1]["C"]>0)? "Tiene <strong>".$rresponse["results"][0]["C"]."</strong>" : "No tiene")." tareas por vencer.",
							"%t_nuevas%" => (($rresponse["results"][2]["C"]>0)? "Tiene <strong>".$rresponse["results"][0]["C"]."</strong>" : "No tiene")." tareas nuevas.");
			$headers = array();
			$headers[] = "Message-Id: ".EmailMessageID();
			$headers[] = "MIME-Version: 1.0";
			$headers[] = "Content-type: text/html; charset=utf-8";
			$headers[] = "From: ".$emailnotifications;
			$headers[] = "Reply-To: ".$emailnotifications;
			$headers[] = "Return-Path: ".$emailnotifications;
			$headers[] = "X-Priority: 2";
  			$headers[] = "X-Mailer: PHP/".phpversion();	
			TemplatedEmail($sendto, $subject, $email, $bidings, $headers);
		}
	}
}
?>