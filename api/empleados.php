<?php
if (($api2==0) && (($rparams["ID_USERGROUP"]==1) || ($rparams["ID_EMPLEADO"]==$rparams["IDEMPLEADO"]))){
	$eparams=array("api" => ($rparams["id"]=="0") ? "31" : "41",
											"t" => $rparams["t"],
											"IDEMPLEADO" => $rparams["id"],
											"NOMBRE" => $rparams["NOMBRE"],
											"APELLIDO" => $rparams["APELLIDO"],
											"TELEFONOS" => $rparams["TELEFONOS"],
											"CORREO" => $rparams["CORREO"]);
	if (isset($rparams["IDSUCURSAL"])){
		$eparams["IDSUCURSAL"]=$rparams["IDSUCURSAL"];
	}
	if (isset($rparams["IDDEPARTAMENTO"])){
		$eparams["IDDEPARTAMENTO"]=$rparams["IDDEPARTAMENTO"];
	}
	if (isset($rparams["IDEMPLEADO_S"])){
		$eparams["IDEMPLEADO_S"]=$rparams["IDEMPLEADO_S"];
	}
	if (isset($rparams["NUSUARIO"])){
		$eparams["NUSUARIO"]=strtoupper($rparams["NUSUARIO"]);
		if (isset($rparams["CUSUARIO"]) && (trim($rparams["CUSUARIO"])!='')){
			$eparams["CUSUARIO"]=SaltedHASH($rparams["CUSUARIO"],":".$eparams["NUSUARIO"].":");
		}
	}
	if ($rparams["id"]=="0"){
		$rparams["api"]="31";
		$api=3;
		$api2=1;
		print json_encode(SysDBInsert($eparams, $gdb));
	}else{
		$rparams["api"]="41";
		$api=4;
		$api2=1;
		print json_encode(SysDBUpdate($eparams, $gdb));
	}
}elseif ($api2==0){
	http_response_code(403);
}
if ($api2==2){
	if ($action=="0"){
		$rparams["api"]="21";
		$api=2;
		$api2=1;
		$sqwhere="";
		$sqselect="SELECT `empleados`.`IDEMPLEADO` AS `ID`, `empleados`.`NOMBRE` AS `C1`, `empleados`.`APELLIDO` AS `C2`, `empleados`.`TELEFONOS` AS `C3`, ".
					"`empleados`.`CORREO` AS `C4`, `empleados`.`IDSUCURSAL` AS `C5`, `sucursales`.`NOMBRE` AS `C6`, `empleados`.`IDDEPARTAMENTO` AS `C7`, ".
					"`departamentos`.`NOMBRE` AS `C8`, `empleados`.`IDEMPLEADO_S` AS `C9`, CONCAT(`supervisores`.`NOMBRE`, ' ', `supervisores`.`APELLIDO`) AS `C10`, ".
					"`empleados`.`NUSUARIO` AS `C11` ".
					"FROM ((`empleados` LEFT JOIN `sucursales` on `empleados`.`IDSUCURSAL` =`sucursales`.`IDSUCURSAL`) ".
										"LEFT JOIN `departamentos` on `empleados`.`IDDEPARTAMENTO` =`departamentos`.`IDDEPARTAMENTO`) ".
										"LEFT JOIN `empleados` as `supervisores` on `empleados`.`IDEMPLEADO_S` =`supervisores`.`IDEMPLEADO`";	
		$sqwhere=" WHERE (((`empleados`.`IDEMPLEADO`)=:IDEMPLEADO))";
		print json_encode(SysDBSelect(array("api" => "21",
											"t" => $rparams["t"],
											"IDEMPLEADO" => $rparams["id"]), $gdb));
	}
	if ($action=="1"){
		$rparams["api"]="21";
		$api=2;
		$api2=1;
		$squote="";
		$sqwhere="";
		$sqselect="SELECT `empleados`.`IDEMPLEADO` AS `ID`, `empleados`.`NCOMPLETO` AS `C1`, `departamentos`.`NOMBRE` AS `C2` FROM `empleados` LEFT JOIN `departamentos` on `empleados`.`IDDEPARTAMENTO` =`departamentos`.`IDDEPARTAMENTO`";	
		$sqwhere=" WHERE (((`empleados`.`ACTIVO`)=1) AND ((`empleados`.`NCOMPLETO`) REGEXP :NOMBRE))";
		$sqorder=" ORDER BY `empleados`.`NCOMPLETO`";
		print json_encode(SysDBSelect(array("api" => "21",
											"t" => $rparams["t"],
											"p" => $rparams["p"],
											"np" => $rparams["np"],
											"NOMBRE" => FormatSearchS($rparams["name"]),
											"aliaslist" => array("NOMBRE" => "name")), $gdb));
	}
}
if (($api2==5) && ($rparams["ID_USERGROUP"]==1)){
	/*$rparams["api"]="51";
	$api=5;
	$api2=1;
	print json_encode(SysDBDelete(array("api" => "51",
										"t" => $rparams["t"],
										"IDEMPLEADO" => $rparams["id"]), $gdb));*/
	$rparams["api"]="4";
	$api=4;
	$api2=1;
	print json_encode(SysDBUpdate(array("api" => "41",
										"t" => $rparams["t"],
										"IDEMPLEADO" => $rparams["id"],
										"ACTIVO" => 0), $gdb));
}elseif ($api2==5){
	http_response_code(403);
}

?>