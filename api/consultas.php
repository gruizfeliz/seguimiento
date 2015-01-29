<?php
if ($api2==2){
	$datefmt=isset($rparams["d"]) ? DateFormatString($rparams["d"]) : "%m/%d/%Y";
	$option=isset($rparams["o"])? intval($rparams["o"]) : 0;
	if ($action=="0"){

	}
	if ($action=="1"){
		if ($option==0){
			$rparams["api"]="21";
			$api=2;
			$api2=1;
			$squote="";
			$sqwhere="";
			$sqselect="SELECT `tareas`.`IDEMPLEADO` AS `ID`, `empleados`.`NCOMPLETO` AS `C1`, COUNT(`tareas`.`IDTAREA`) AS `C2` FROM `tareas` LEFT JOIN `empleados` on `tareas`.`IDEMPLEADO` = `empleados`.`IDEMPLEADO`";	
			$sqwhere=" WHERE ((`tareas`.`IDESTATUS`<3) AND ((`empleados`.`NCOMPLETO`) REGEXP :DESCRIPCION))".
						" GROUP BY `tareas`.`IDEMPLEADO`";
			$sqorder=" ORDER BY `empleados`.`NCOMPLETO`";
			$sqselect_c="SELECT COUNT(*) AS RCOUNT FROM (".$sqselect.$sqwhere.") AS `tareasempleados`";
			$eparams=array("api" => "21",
							"t" => "tareas",
							"p" => $rparams["p"],
							"np" => $rparams["np"],
							"DESCRIPCION" => FormatSearchS($rparams["name"]),
							"aliaslist" => array("DESCRIPCION" => "name"));
			print json_encode(SysDBSelect($eparams, $gdb));
		}
	}
	if ($action=="3"){
		if ($option==0){
			$rparams["api"]="21";
			$api=2;
			$api2=1;
			$sqwhere="";
			$sqselect="SELECT `tareas`.`IDTAREA` AS `ID`, DATE_FORMAT(`tareas`.`FECHA`,'".$datefmt."') AS `C1`, `tareas`.`IDCASO` AS `C2`, `casos`.`DETALLES` AS `C3`, ".
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
			$sqwhere=" WHERE (((`tareas`.`IDEMPLEADO`)=:IDEMPLEADO))";
			$sqorder=" ORDER BY IF(((`tareas`.`FECHAENTREGA`>=NOW()) OR (`tareas`.`IDESTATUS`>2)),`tareas`.`IDESTATUS`, 0), `tareas`.`FECHA`";
			print json_encode(SysDBSelect(array("api" => "21",
												"t" => "tareas",
												"p" => $rparams["p"],
												"np" => $rparams["np"],
												"IDEMPLEADO" => $rparams["id"]), $gdb), JSON_UNESCAPED_UNICODE);
		}
	}
}

?>