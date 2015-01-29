<?php
if ($api2==0){
	$eparams=array("api" => ($rparams["id"]=="0") ? "31" : "41",
											"t" => $rparams["t"],
											"IDTAREA" => $rparams["id"],
											"DESCRIPCION" => $rparams["DESCRIPCION"],
											"DETALLES" => $rparams["DETALLES"],
											"FECHAENTREGA" => $rparams["FECHAENTREGA"],
											"RECORDATORIO" => $rparams["RECORDATORIO"]);
	if (isset($rparams["IDCASO"])){
		$eparams["IDCASO"]=$rparams["IDCASO"];
	}
	if (isset($rparams["IDEMPLEADO"])){
		$eparams["IDEMPLEADO"]=$rparams["IDEMPLEADO"];
	}
	if (isset($rparams["IDEMPLEADO_S"])){
		$eparams["IDEMPLEADO_S"]=$rparams["IDEMPLEADO_S"];
	}
	if (isset($rparams["CONCLUSIONES"])){
		$eparams["FECHACIERRE"]=$rparams["FECHACIERRE"];
		$eparams["CONCLUSIONES"]=$rparams["CONCLUSIONES"];
		$eparams["IDESTATUS"]=3;
	}else{
		$eparams["FECHACIERRE"]=null;
		$eparams["IDESTATUS"]=2;
	}
	if ($rparams["id"]=="0"){
	    $eparams["IDEMPLEADO_C"]=$rparams["ID_EMPLEADO"];   
		$rparams["api"]="3";
		$api=3;
		$api2=1;
		$response=SysDBInsert($eparams, $gdb);
		print json_encode($response);
	}else{
		$rparams["api"]="41";
		$api=4;
		$api2=1;
		$response=SysDBUpdate($eparams, $gdb);
		print json_encode($response);
	}
	if (($rparams["id"]=="0") || (isset($rparams["CONCLUSIONES"]))){
		$rparams["api"]="21";
		$api=2;
		$api2=1;
		$sqwhere="";
		$sqselect="SELECT `tareas`.`IDTAREA` AS `ID`, DATE_FORMAT(`tareas`.`FECHA`,'".$datefmt."') AS `C1`, `tareas`.`IDCASO` AS `C2`, `casos`.`DETALLES` AS `C3`, ".
					"`empleados`.`CORREO` AS `C4`, `empleados`.`NCOMPLETO` AS `C5`, `supervisores`.`CORREO` AS `C6`, ".
					"CONCAT(`supervisores`.`NOMBRE`, ' ', `supervisores`.`APELLIDO`) AS `C7`, `tareas`.`DESCRIPCION` AS `C8`, `tareas`.`DETALLES` AS `C9`, ".
					"DATE_FORMAT(`tareas`.`FECHAENTREGA`,'".$datefmt."') AS `C10`, `tareas`.`RECORDATORIO` AS `C11`, DATE_FORMAT(`tareas`.`FECHACIERRE`,'".$datefmt."') AS `C12`, ".
					"`tareas`.`CONCLUSIONES` AS `C13`, `estatus`.`DESCRIPCION` AS `C14`, `tareas`.`IDEMPLEADO_C` AS `C15`, ".
					"CONCAT(`creadores`.`NOMBRE`, ' ', `creadores`.`APELLIDO`) AS `C16`, `creadores`.`CORREO` AS `C17`".
					"FROM ((((`tareas` LEFT JOIN `casos` on `tareas`.`IDCASO` =`casos`.`IDCASO`) ".
										"LEFT JOIN `empleados` on `tareas`.`IDEMPLEADO` =`empleados`.`IDEMPLEADO`) ".
										"LEFT JOIN `empleados` as `supervisores` on `tareas`.`IDEMPLEADO_S` =`supervisores`.`IDEMPLEADO`) ".
										"LEFT JOIN `empleados` as `creadores` on `tareas`.`IDEMPLEADO_C` =`creadores`.`IDEMPLEADO`) ".
										"LEFT JOIN `estatus` on `tareas`.`IDESTATUS` =`estatus`.`IDESTATUS`";
		$sqwhere=" WHERE (((`tareas`.`IDTAREA`)=:IDTAREA))";
		//print_r($sqselect.$sqwhere
		//print_r($response);
		$response=SysDBSelect(array("api" => "21",
									"t" => $rparams["t"],
									"IDTAREA" => $response["ID"]), $gdb);
		//print_r($response);
		
		//$sendto=GetUserEmail($rparams["ID_EMPLEADO"], $gdb);
		$sendto=$response["results"][0]["C4"];
		if (isset($rparams["CONCLUSIONES"])){
			$subject="Seguimiento Cierre Tarea";
			$email='
<html>
<head>
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
   <title>'.$subject.'</title>
</head>
<body>
   <p>Cierre Tarea</p>
   <p>Fecha de Entrega: %FECHAENTREGA%</p>
   <p>Descripcion: %DESCRIPCION%</p>
   <p>Detalles: %DETALLES%</p>
   <p>Conclusiones: %CONCLUSIONES%</p>
   <p>Creado por: %CREADOR%</p>
   <p>Mensaje automatico no responder.</p>
</body>
</html>';
			$bidings=Array ("%FECHAENTREGA%" => $response["results"][0]["C10"],
							"%FECHACIERRE%" => $response["results"][0]["C12"],
							"%DESCRIPCION%" => $rparams["DESCRIPCION"],
							"%DETALLES%" => $rparams["DETALLES"],
							"%CONCLUSIONES%" => $rparams["CONCLUSIONES"],
							"%CREADOR%" => $response["results"][0]["C16"]." <".$response["results"][0]["C17"].">");		
		}else{
			$subject="Seguimiento Nueva Tarea";
			$email='
<html>
<head>
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
   <title>'.$subject.'</title>
</head>
<body>
   <p>Nueva Tarea</p>
   <p>Fecha de Entrega: %FECHAENTREGA%</p>
   <p>Descripcion: %DESCRIPCION%</p>
   <p>Detalles: %DETALLES%</p>
   <p>Creado por: %CREADOR%</p>
   <p>Mensaje automatico no responder.</p>
</body>
</html>';
			$bidings=Array ("%FECHAENTREGA%" => $response["results"][0]["C10"],
							"%DESCRIPCION%" => $rparams["DESCRIPCION"],
							"%DETALLES%" => $rparams["DETALLES"],
							"%CREADOR%" => $response["results"][0]["C16"]." <".$response["results"][0]["C17"].">");
		}
		$headers = array();
		$headers[] = "Message-Id: ".EmailMessageID();
		$headers[] = "MIME-Version: 1.0";
		$headers[] = "Content-type: text/html; charset=utf-8";
		$headers[] = "From: ".$emailnotifications;
		$headers[] = "Reply-To: ".$emailnotifications;
		$headers[] = "Return-Path: ".$emailnotifications;
		if ((strlen($response["results"][0]["C6"])>0) && ($response["results"][0]["C6"]!=$sendto)){
			$ccs[]=$response["results"][0]["C7"]." <".$response["results"][0]["C6"].">";
		}
		if ((strlen($response["results"][0]["C17"])>0) && ($response["results"][0]["C17"]!=$sendto)){
			$ccs[]=$response["results"][0]["C16"]." <".$response["results"][0]["C17"].">";
		}
		if (isset($ccs)){
			$headers[] = "Cc: ".implode(", ", $ccs);
		}
		$headers[] = "X-Priority: 2";
		$headers[] = "X-Mailer: PHP/".phpversion();	
		TemplatedEmail($sendto, $subject, $email, $bidings, $headers);
	}
}
if ($api2==2){
	if ($action=="0"){
		$rparams["api"]="21";
		$api=2;
		$api2=1;
		$sqwhere="";
		$sqselect="SELECT `tareas`.`IDTAREA` AS `ID`, DATE_FORMAT(`tareas`.`FECHA`,'".$datefmt."') AS `C1`, `tareas`.`IDCASO` AS `C2`, `casos`.`ORIGEN` AS `C3`, ".
					"`tareas`.`IDEMPLEADO` AS `C4`, `empleados`.`NCOMPLETO` AS `C5`, `tareas`.`IDEMPLEADO_S` AS `C6`, ".
					"CONCAT(`supervisores`.`NOMBRE`, ' ', `supervisores`.`APELLIDO`) AS `C7`, `tareas`.`DESCRIPCION` AS `C8`, `tareas`.`DETALLES` AS `C9`, ".
					"DATE_FORMAT(`tareas`.`FECHAENTREGA`,'".$datefmt."') AS `C10`, `tareas`.`RECORDATORIO` AS `C11`, DATE_FORMAT(`tareas`.`FECHACIERRE`,'".$datefmt."') AS `C12`, ".
					"`tareas`.`CONCLUSIONES` AS `C13`, `estatus`.`DESCRIPCION` AS `C14`, `tareas`.`IDEMPLEADO_C` AS `C15`, ".
					"CONCAT(`creadores`.`NOMBRE`, ' ', `creadores`.`APELLIDO`) AS `C16` ".
					"FROM ((((`tareas` LEFT JOIN `casos` on `tareas`.`IDCASO` =`casos`.`IDCASO`) ".
										"LEFT JOIN `empleados` on `tareas`.`IDEMPLEADO` =`empleados`.`IDEMPLEADO`) ".
										"LEFT JOIN `empleados` as `supervisores` on `tareas`.`IDEMPLEADO_S` =`supervisores`.`IDEMPLEADO`) ".
										"LEFT JOIN `empleados` as `creadores` on `tareas`.`IDEMPLEADO_C` =`creadores`.`IDEMPLEADO`) ".
										"LEFT JOIN `estatus` on `tareas`.`IDESTATUS` =`estatus`.`IDESTATUS`";
		$sqwhere=" WHERE (((`tareas`.`IDTAREA`)=:IDTAREA))";
		//print_r($sqselect.$sqwhere);
		print json_encode(SysDBSelect(array("api" => "21",
											"t" => $rparams["t"],
											"IDTAREA" => $rparams["id"]), $gdb));
	}
	if ($action=="1"){
		$rparams["api"]="21";
		$api=2;
		$api2=1;
		$squote="";
		$sqwhere="";
		$option=isset($rparams["o"])? intval($rparams["o"]) : 0;
		$soption="";
		if (($option>0) && ($option<6)){
			$soption="((IF(((`tareas`.`FECHAENTREGA`>=NOW()) OR (`tareas`.`IDESTATUS`>2)),`tareas`.`IDESTATUS`, 0))= :IDESTATUS) AND ";
			$eparams=array("api" => "21",
							"t" => $rparams["t"],
							"p" => $rparams["p"],
							"np" => $rparams["np"],
							"DESCRIPCION" => FormatSearchS($rparams["name"]),
							"IDEMPLEADO" => $rparams["ID_EMPLEADO"],
							"IDESTATUS" => ($option-1),
							"aliaslist" => array("DESCRIPCION" => "name"));
			$sqorder=" ORDER BY `tareas`.`FECHA`";
		}else{
			if ($option>5){
				$soption="(((`tareas`.`IDESTATUS`)<3) AND ((`FECHAENTREGA`)>NOW()) AND ((ADDDATE(`FECHAENTREGA`, -`RECORDATORIO`))<=NOW())) AND ";
			}		
			$eparams=array("api" => "21",
							"t" => $rparams["t"],
							"p" => $rparams["p"],
							"np" => $rparams["np"],
							"DESCRIPCION" => FormatSearchS($rparams["name"]),
							"IDEMPLEADO" => $rparams["ID_EMPLEADO"],
							"aliaslist" => array("DESCRIPCION" => "name"));
			$sqorder=" ORDER BY IF(((`tareas`.`FECHAENTREGA`>=NOW()) OR (`tareas`.`IDESTATUS`>2)),`tareas`.`IDESTATUS`, 0), `tareas`.`FECHA`";
		}		
		$sqselect="SELECT `tareas`.`IDTAREA` AS `ID`, CONCAT(DATE_FORMAT(`tareas`.`FECHAENTREGA`,'".$datefmt."'), IF(((`tareas`.`IDEMPLEADO`)=:IDEMPLEADO),' - R',''), IF(((`tareas`.`IDEMPLEADO_S`)=:IDEMPLEADO),' - S',''), ' - ',`estatus`.`CODIGO`) AS `C1`, `tareas`.`DESCRIPCION` AS `C2` FROM `tareas` LEFT JOIN `estatus` on IF(((`tareas`.`FECHAENTREGA`>=NOW()) OR (`tareas`.`IDESTATUS`>2)),`tareas`.`IDESTATUS`, 0) = `estatus`.`IDESTATUS`";
		$sqwhere=" WHERE (".$soption."(((`tareas`.`IDEMPLEADO`) REGEXP :IDEMPLEADO) OR ((`tareas`.`IDEMPLEADO_S`) REGEXP :IDEMPLEADO)) AND (((`tareas`.`DESCRIPCION`) REGEXP :DESCRIPCION) OR ((`tareas`.`DETALLES`) REGEXP :DESCRIPCION)))";
		print json_encode(SysDBSelect($eparams, $gdb));
	}
	if ($action=="2"){
		$rparams["api"]="21";
		$api=2;
		$api2=1;
		$squote="";
		$sqwhere="";
		$sqselect="SELECT `tareas`.`DESCRIPCION` AS `C1`  FROM `tareas`";	
		$sqwhere=" WHERE (((`tareas`.`DESCRIPCION`) REGEXP :DESCRIPCION)) GROUP BY `tareas`.`DESCRIPCION`";
		$sqorder=" ORDER BY `tareas`.`DESCRIPCION`";
		print json_encode(SysDBSelect(array("api" => "21",
											"t" => $rparams["t"],
											"p" => $rparams["p"],
											"np" => $rparams["np"],
											"DESCRIPCION" => FormatSearchS($rparams["name"]),
											"aliaslist" => array("DESCRIPCION" => "name")), $gdb));
	}
	if ($action=="3"){
		$rparams["api"]="21";
		$api=2;
		$api2=1;
		$sqwhere="";
		$sqselect="SELECT `tareas`.`IDTAREA` AS `ID`, DATE_FORMAT(`tareas`.`FECHA`,'".$datefmt."') AS `C1`, `tareas`.`IDCASO` AS `C2`, `casos`.`ORIGEN` AS `C3`, ".
					"`tareas`.`IDEMPLEADO` AS `C4`, `empleados`.`NCOMPLETO` AS `C5`, `tareas`.`IDEMPLEADO_S` AS `C6`, ".
					"CONCAT(`supervisores`.`NOMBRE`, ' ', `supervisores`.`APELLIDO`) AS `C7`, `tareas`.`DESCRIPCION` AS `C8`, `tareas`.`DETALLES` AS `C9`, ".
					"DATE_FORMAT(`tareas`.`FECHAENTREGA`,'".$datefmt."') AS `C10`, `tareas`.`RECORDATORIO` AS `C11`, DATE_FORMAT(`tareas`.`FECHACIERRE`,'".$datefmt."') AS `C12`, ".
					"`tareas`.`CONCLUSIONES` AS `C13`, `estatus`.`DESCRIPCION` AS `C14`, `tareas`.`IDEMPLEADO_C` AS `C15`, ".
					"CONCAT(`creadores`.`NOMBRE`, ' ', `creadores`.`APELLIDO`) AS `C16` ".
					"FROM ((((`tareas` LEFT JOIN `casos` on `tareas`.`IDCASO` =`casos`.`IDCASO`) ".
										"LEFT JOIN `empleados` on `tareas`.`IDEMPLEADO` =`empleados`.`IDEMPLEADO`) ".
										"LEFT JOIN `empleados` as `supervisores` on `tareas`.`IDEMPLEADO_S` =`supervisores`.`IDEMPLEADO`) ".
										"LEFT JOIN `empleados` as `creadores` on `tareas`.`IDEMPLEADO_C` =`creadores`.`IDEMPLEADO`) ".
										"LEFT JOIN `estatus` on `tareas`.`IDESTATUS` =`estatus`.`IDESTATUS`";
		$sqwhere=" WHERE (((`tareas`.`IDCASO`)=:IDCASO))";
		$sqorder=" ORDER BY IF(((`tareas`.`FECHAENTREGA`>=NOW()) OR (`tareas`.`IDESTATUS`>2)),`tareas`.`IDESTATUS`, 0), `tareas`.`FECHA`";
		print json_encode(SysDBSelect(array("api" => "21",
											"t" => $rparams["t"],
											"p" => $rparams["p"],
											"np" => $rparams["np"],
											"IDCASO" => $rparams["id"],
											"aliaslist" => array("IDCASO" => "id")), $gdb), JSON_UNESCAPED_UNICODE);
	}
}
/*if ($api2==5){
	$rparams["api"]="51";
	$api=5;
	$api2=1;
	print json_encode(SysDBDelete(array("api" => "51",
										"t" => $rparams["t"],
										"IDTAREA" => $rparams["id"]), $gdb));
	$rparams["api"]="4";
	$api=4;
	$api2=1;
	print json_encode(SysDBUpdate(array("api" => "41",
										"t" => $rparams["t"],
										"IDEMPLEADO" => $rparams["id"],
										"ACTIVO" => 0), $gdb));
}*/

?>