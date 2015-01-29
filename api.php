<?php
include_once "./config.php";
include_once "./api/sys/db.php";
include_once "./api/sys/funcs.php";
include_once "./api/sys/sec/sys.php";

if(isset($_REQUEST['api'])){
	$rparams=$_REQUEST;
	$rdata=json_encode($rparams, JSON_FORCE_OBJECT);
}else{
	$rdata = file_get_contents("php://input");
	$rparams = json_decode($rdata, true);
}
//var_dump($rparams);
settype($rparams["api"], "string");
$api=isset($rparams["api"][0])? intval($rparams["api"][0]) : Null;
$api2=isset($rparams["api"][1])? intval($rparams["api"][1]) : 0;
$action=isset($rparams["a"])? $rparams["a"] : "0";
$session=isset($rparams["tk"])? GetSessions($rparams["tk"], $gdb) : Null;
$datefmt=isset($rparams["d"]) ? DateFormatString($rparams["d"]) : "%m/%d/%Y";

LogRequest(array("IDEMPLEADO" => 0, "api" => "1", "t" => "prueba", "rdata" => $rdata.' IP:'.GetIPAddress()), $gdb);

if ((isset($session)) || (($api===0) && ($api2===3) && ($rparams["t"]==="users"))){
	$rparams["ID_EMPLEADO"]=isset($session["IDEMPLEADO"])? $session["IDEMPLEADO"] : 0;
	$rparams["ID_USERGROUP"]=isset($session["IDUSERGROUP"])? $session["IDUSERGROUP"] : 0;
	unset($session);
	LogRequest(array("IDEMPLEADO" => $rparams["ID_EMPLEADO"], "api" => $rparams["api"], "t" => $rparams["t"], "rdata" => $rdata), $gdb);
	if ($api===0){//Special Cases
		include_once "./api/".$rparams["t"].".php";
	}elseif(isset($sysdbblock[$rparams["t"]])){
		http_response_code(401);
	}else{
		if($api==1){//Table Structure & Default Values
			print json_encode(GetTableStructure(0,$rparams["t"], $gdb), JSON_FORCE_OBJECT);
		}elseif ($api==2){//Simple Selects
			print SysDBSelect($rparams, $gdb);
		}elseif ($api==3){//Insert
			print SysDBInsert($rparams, $gdb);
		}elseif ($api==4){//Update
			print SysDBUpdate($rparams, $gdb);
		}elseif ($api==5){//Delete
			print SysDBDelete($rparams, $gdb);
		}else{
			http_response_code(404);
		}
	}
}else{
	http_response_code(401);
}

//Closing Connections
$gdb=null;

?>