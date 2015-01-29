<?php
if (($api2==0) && ($rparams["ID_USERGROUP"]==1)){
	if ($rparams["id"]=="0"){
		$rparams["api"]="3";
		$api=3;
		$api2=1;
		print json_encode(SysDBInsert(array("api" => "31",
											"t" => $rparams["t"],
											"NOMBRE" => $rparams["NOMBRE"],
											"DIRECCION" => $rparams["DIRECCION"]), $gdb));
	}else{
		$rparams["api"]="4";
		$api=4;
		$api2=1;
		print json_encode(SysDBUpdate(array("api" => "41",
											"t" => $rparams["t"],
											"IDSUCURSAL" => $rparams["id"],
											"NOMBRE" => $rparams["NOMBRE"],
											"DIRECCION" => $rparams["DIRECCION"]), $gdb));
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
		$sqselect="SELECT `sucursales`.`IDSUCURSAL` AS `ID`, `sucursales`.`NOMBRE` AS `C1`, `DIRECCION` AS `C2` FROM `sucursales`";
		$sqwhere=" WHERE (((`sucursales`.`IDSUCURSAL`)=:IDSUCURSAL))";
		print json_encode(SysDBSelect(array("api" => "21",
											"t" => $rparams["t"],
											"IDSUCURSAL" => $rparams["id"]), $gdb));
	}
	if ($action=="1"){
		$rparams["api"]="21";
		$api=2;
		$api2=1;
		$squote="";
		$sqwhere="";
		$sqselect="SELECT `IDSUCURSAL` AS `ID`, `NOMBRE` AS `C1`, `DIRECCION` AS `C2` FROM `sucursales`";	
		$sqwhere=" WHERE (((`sucursales`.`ACTIVO`)=1) AND ((`sucursales`.`NOMBRE`) REGEXP :NOMBRE))";
		$sqorder=" ORDER BY `sucursales`.`IDSUCURSAL`";
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
										"IDSUCURSAL" => $rparams["id"]), $gdb));*/
	$rparams["api"]="4";
	$api=4;
	$api2=1;
	print json_encode(SysDBUpdate(array("api" => "41",
										"t" => $rparams["t"],
										"IDSUCURSAL" => $rparams["id"],
										"ACTIVO" => 0), $gdb));
}elseif ($api2==5){
	http_response_code(403);
}

?>