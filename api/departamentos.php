<?php
if (($api2==0) && ($rparams["ID_USERGROUP"]==1)){
	if ($rparams["id"]=="0"){
		$rparams["api"]="3";
		$api=3;
		$api2=1;
		print json_encode(SysDBInsert(array("api" => "31",
											"t" => $rparams["t"],
											"IDDEPARTAMENTO" => $rparams["id"],
											"NOMBRE" => $rparams["NOMBRE"],
											"IDEMPLEADO" => $rparams["IDEMPLEADO"]), $gdb));
	}else{
		$rparams["api"]="4";
		$api=4;
		$api2=1;
		print json_encode(SysDBUpdate(array("api" => "41",
											"t" => $rparams["t"],
											"IDDEPARTAMENTO" => $rparams["id"],
											"NOMBRE" => $rparams["NOMBRE"],
											"IDEMPLEADO" => $rparams["IDEMPLEADO"]), $gdb));
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
		$sqselect="SELECT `departamentos`.`IDDEPARTAMENTO` AS `ID`, `departamentos`.`NOMBRE` AS `C1`, `departamentos`.`IDEMPLEADO` AS `C2`, `empleados`.`NCOMPLETO` AS `C3` FROM `departamentos` LEFT JOIN `empleados` on `departamentos`.`IDEMPLEADO` =`empleados`.`IDEMPLEADO`";
		$sqwhere=" WHERE (((`departamentos`.`IDDEPARTAMENTO`)=:IDDEPARTAMENTO))";
		print json_encode(SysDBSelect(array("api" => "21",
											"t" => $rparams["t"],
											"IDDEPARTAMENTO" => $rparams["id"]), $gdb));
	}
	if ($action=="1"){
		$rparams["api"]="21";
		$api=2;
		$api2=1;
		$squote="";
		$sqwhere="";
		$sqselect="SELECT `departamentos`.`IDDEPARTAMENTO` AS `ID`, `departamentos`.`NOMBRE` AS `C1`, `empleados`.`NCOMPLETO` AS `C2` FROM `departamentos` LEFT JOIN `empleados` on `departamentos`.`IDEMPLEADO` =`empleados`.`IDEMPLEADO`";
		$sqwhere=" WHERE (((`departamentos`.`ACTIVO`)=1) AND ((`departamentos`.`NOMBRE`) REGEXP :NOMBRE))";
		$sqorder=" ORDER BY `departamentos`.`IDDEPARTAMENTO`";
		print json_encode(SysDBSelect(array("api" => "21",
											"t" => $rparams["t"],
											"p" => $rparams["p"],
											"np" => $rparams["np"],
											"NOMBRE" => FormatSearchS($rparams["name"])), $gdb));
	}
}
if (($api2==5) && ($rparams["ID_USERGROUP"]==1)){
	/*$rparams["api"]="51";
	$api=5;
	$api2=1;
	print json_encode(SysDBDelete(array("api" => "51",
										"t" => $rparams["t"],
										"IDDEPARTAMENTO" => $rparams["id"]), $gdb));*/
	$rparams["api"]="4";
	$api=4;
	$api2=1;
	print json_encode(SysDBUpdate(array("api" => "41",
										"t" => $rparams["t"],
										"IDDEPARTAMENTO" => $rparams["id"],
										"ACTIVO" => 0), $gdb));
}elseif ($api2==5){
	http_response_code(403);
}

?>