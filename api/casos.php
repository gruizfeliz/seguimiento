<?php
if ($api2==0){
	$eparams=array("api" => ($rparams["id"]=="0") ? "31" : "41",
											"t" => $rparams["t"],
											"IDCASO" => $rparams["id"],
											"ORIGEN" => $rparams["ORIGEN"],
											"DETALLES" => $rparams["DETALLES"],
											"CONTACTO" => $rparams["CONTACTO"],
											"TELEFONOS" => $rparams["TELEFONOS"],
											"CORREO" => $rparams["CORREO"]);
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
		$eparams["IDESTATUS"]=2;
	}
	if ($rparams["id"]=="0"){
	    $eparams["IDEMPLEADO_C"]=$rparams["ID_EMPLEADO"];   
		$rparams["api"]="3";
		$api=3;
		$api2=1;
		print json_encode(SysDBInsert($eparams, $gdb));
	}else{
		$rparams["api"]="41";
		$api=4;
		$api2=1;
		print json_encode(SysDBUpdate($eparams, $gdb));
	}
}
if ($api2==2){
	$datefmt=isset($rparams["d"]) ? DateFormatString($rparams["d"]) : "%m/%d/%Y";
	if ($action=="0"){
		$rparams["api"]="21";
		$api=2;
		$api2=1;
		$sqwhere="";
		$sqselect="SELECT `casos`.`IDCASO` AS `ID`, DATE_FORMAT(`casos`.`FECHA`,'".$datefmt."') AS `C1`, `casos`.`IDEMPLEADO` AS `C2`, ".
					"`empleados`.`NCOMPLETO` AS `C3`, `casos`.`ORIGEN` AS `C4`, `casos`.`DETALLES` AS `C5`, `casos`.`CONTACTO` AS `C6`, ".
					"`casos`.`TELEFONOS` AS `C7`, `casos`.`CORREO` AS `C8`, DATE_FORMAT(`casos`.`FECHACIERRE`,'".$datefmt."') AS `C9`, ".
					"`casos`.`CONCLUSIONES` AS `C10`, `estatus`.`DESCRIPCION` AS `C11` ".
					"FROM (`casos` LEFT JOIN `empleados` on `casos`.`IDEMPLEADO` =`empleados`.`IDEMPLEADO`) ".
										"LEFT JOIN `estatus` on `casos`.`IDESTATUS` =`estatus`.`IDESTATUS`";	
		$sqwhere=" WHERE (((`casos`.`IDCASO`)=:IDCASO))";
		//print_r($sqselect.$sqwhere);
		print json_encode(SysDBSelect(array("api" => "21",
											"t" => $rparams["t"],
											"IDCASO" => $rparams["id"]), $gdb));
	}
	if ($action=="1"){
		$rparams["api"]="21";
		$api=2;
		$api2=1;
		$squote="";
		$sqwhere="";
		$sqselect="SELECT `casos`.`IDCASO` AS `ID`, CONCAT(DATE_FORMAT(`casos`.`FECHA`,'".$datefmt."'), ' - ',`estatus`.`CODIGO`) AS `C1`, `casos`.`ORIGEN` AS `C2` FROM `casos` LEFT JOIN `estatus` on `casos`.`IDESTATUS` =`estatus`.`IDESTATUS`";	
		$sqwhere=" WHERE (((`casos`.`ORIGEN`) REGEXP :ORIGEN) OR ((`casos`.`DETALLES`) REGEXP :ORIGEN))";
		$sqorder=" ORDER BY `casos`.`IDESTATUS`, `casos`.`FECHA`";
		//print_r($sqselect.$sqwhere);
		print json_encode(SysDBSelect(array("api" => "21",
											"t" => $rparams["t"],
											"p" => $rparams["p"],
											"np" => $rparams["np"],
											"ORIGEN" => FormatSearchS($rparams["name"]),
											"aliaslist" => array("ORIGEN" => "name")), $gdb));
	}
	if ($action=="2"){
		$rparams["api"]="21";
		$api=2;
		$api2=1;
		$squote="";
		$sqwhere="";
		$sqselect="SELECT `casos`.`IDCASO` AS `ID`,  `casos`.`ORIGEN` AS `C1`  FROM `casos`";	
		$sqwhere=" WHERE (((`casos`.`ORIGEN`) REGEXP :ORIGEN) OR ((`casos`.`DETALLES`) REGEXP :ORIGEN) OR ((`casos`.`CONTACTO`) REGEXP :ORIGEN)) GROUP BY `casos`.`ORIGEN`";
		$sqorder=" ORDER BY `casos`.`ORIGEN`";
		print json_encode(SysDBSelect(array("api" => "21",
											"t" => $rparams["t"],
											"p" => $rparams["p"],
											"np" => $rparams["np"],
											"ORIGEN" => FormatSearchS($rparams["name"]),
											"aliaslist" => array("ORIGEN" => "name")), $gdb));
	}
}
/*if ($api2==5){
	$rparams["api"]="51";
	$api=5;
	$api2=1;
	print json_encode(SysDBDelete(array("api" => "51",
										"t" => $rparams["t"],
										"IDCASO" => $rparams["id"]), $gdb));
	$rparams["api"]="4";
	$api=4;
	$api2=1;
	print json_encode(SysDBUpdate(array("api" => "41",
										"t" => $rparams["t"],
										"IDEMPLEADO" => $rparams["id"],
										"ACTIVO" => 0), $gdb));
}*/

?>