<?php
if ($api2==3){
	if ($action=="3"){
		$qselect=$gdb->prepare("SELECT `empleados`.`IDEMPLEADO`, `empleados`.`IDUSERGROUP`, `empleados`.`NCOMPLETO` AS `NOMBRE` FROM `empleados` WHERE ((`ACTIVO`=1) AND (`NUSUARIO`=:NUSUARIO) AND (`CUSUARIO`=:CUSUARIO));");
		$qselect->bindValue(':NUSUARIO', strtoupper($rparams["uname"]), PDO::PARAM_STR);
		$qselect->bindValue(':CUSUARIO', SaltedHASH($rparams["upass"],":".strtoupper($rparams["uname"]).":"), PDO::PARAM_STR);
		$qselect->execute();
		//$qselect->debugDumpParams();
		//print_r($qselect->errorCode());
		//print_r($qselect->errorInfo());
		//print SaltedHASH($rparams["upass"],":".$rparams["uname"].":");
		$qresults=$qselect->fetchAll(PDO::FETCH_ASSOC);
		//print_r($qresults);
		if (count($qresults)>0){
			session_start();
			$token=SaltedHASH($qresults[0]["IDEMPLEADO"].$qresults[0]["NOMBRE"].":".GetIPAddress().$_SERVER["REMOTE_ADDR"].":", mcrypt_create_iv(22, MCRYPT_DEV_URANDOM));
			$rsessions=$gdb->prepare("INSERT INTO `sessions` (`TOKEN`, `IDEMPLEADO`, `IDUSERGROUP`, `IP_ADDRESS`, `IP_REMOTE`, `LIFE`) VALUES (:token, :IDEMPLEADO, :IDUSERGROUP, :ip_address, :ip_remote, :life);");
			$rsessions->bindValue(':token', $token, PDO::PARAM_INT);
			$rsessions->bindValue(':IDEMPLEADO', $qresults[0]["IDEMPLEADO"], PDO::PARAM_INT);
			$rsessions->bindValue(':IDUSERGROUP', $qresults[0]["IDUSERGROUP"], PDO::PARAM_INT);
			$rsessions->bindValue(':ip_address', GetIPAddress(), PDO::PARAM_STR);
			$rsessions->bindValue(':ip_remote', $_SERVER["REMOTE_ADDR"], PDO::PARAM_STR);
			$rsessions->bindValue(':life', 0, PDO::PARAM_INT);
			$rsessions->execute();
			//$rsessions->debugDumpParams();
			//print_r($rsessions->errorCode());
			//print_r($rsessions->errorInfo());
			
			$_SESSION["token"]=$token;
			$_SESSION["IDEMPLEADO"]=$qresults[0]["IDEMPLEADO"];
			$_SESSION["IDUSERGROUP"]=$qresults[0]["IDUSERGROUP"];
			$_SESSION["ip_address"]=GetIPAddress();
			$_SESSION["ip_remote"]=$_SERVER["REMOTE_ADDR"];
			$_SESSION["life"]=0;
			$rsessions->execute();
			$qresults[0]["TOKEN"]=$token;
			//$qresults[0]["Menus"]=array("NOMBRE" => "Tareas","MANTENIMIENTO" => "tareas");
			$qresults[0]["Menus"]=array(array("NOMBRE" => "Tareas","MANTENIMIENTO" => "tareas"),
										array("NOMBRE" => "Casos","MANTENIMIENTO" => "casos"),
										array("NOMBRE" => "Consultas","MANTENIMIENTO" => "consultas"),
										array("NOMBRE" => "Empleados","MANTENIMIENTO" => "empleados"),
										array("NOMBRE" => "Departamentos","MANTENIMIENTO" => "departamentos"),
										array("NOMBRE" => "Sucursales","MANTENIMIENTO" => "sucursales"));
			/*$qresults[0]["Menus"]=json_decode('{"NOMBRE": "Tareas", "MANTENIMIENTO": "tareas"},
												{"NOMBRE": "Casos", "MANTENIMIENTO": "casos"},
												{"NOMBRE": "Consultas", "MANTENIMIENTO": "consultas"},
												{"NOMBRE": "Empleados", "MANTENIMIENTO": "empleados"},
												{"NOMBRE": "Departamentos", "MANTENIMIENTO": "departamentos"},
												{"NOMBRE": "Sucursales", "MANTENIMIENTO": "sucursales"}');*/
			LogRequest(array("IDEMPLEADO" => $qresults[0]["IDEMPLEADO"], "api" => $rparams["api"], "t" => $rparams["t"], "rdata" => $rdata), $gdb);
			print json_encode($qresults[0]);
		}else{
			http_response_code(401);
		}
	}elseif($action=="4"){
			$action_after="";
	}
}

?>