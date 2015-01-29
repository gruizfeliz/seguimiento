<?php
//Block System Tables from Normal API Access
$sysdbblock=Array("users" => 1,"log_requests" => 1,"sessions" => 1,"sessions_active" => 1);
//$sysdbblock=Null;

//Generate Salted Hash
function SaltedHASH($string, $salt, $times=0) {
	//$salt=md5
	if ((strlen($string)%2)==0){
		return md5(hash( 'sha512', $salt).hash( 'sha512', $string));
	}else{
		return md5(hash( 'sha512', $string).hash( 'sha512', $salt));
	}
}

function GetIPAddress(){
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
        if (isset($_SERVER[$key])){
            foreach (explode(',', $_SERVER[$key]) as $ip){
                $ip = trim($ip); // just to be safe
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                    return $ip;
                }
            }
        }
    }
	return $_SERVER["REMOTE_ADDR"];
}

function LogRequest($rparams, $gdb){
	$rlog=$gdb->prepare("INSERT INTO `log_requests` (`IDEMPLEADO`, `API`, `T`, `RDATA`, `IP_ADDRESS`, `IP_REMOTE`) VALUES ( :IDEMPLEADO,  :api,  :t,  :rdata,  :ip_address,  :ip_remote);");
	$rlog->bindValue(':IDEMPLEADO', $rparams["IDEMPLEADO"], PDO::PARAM_INT);
	$rlog->bindValue(':api', $rparams["api"], PDO::PARAM_STR);
	$rlog->bindValue(':t', $rparams["t"], PDO::PARAM_STR);
	$rlog->bindValue(':rdata', $rparams["rdata"], PDO::PARAM_LOB);
	$rlog->bindValue(':ip_address', GetIPAddress(), PDO::PARAM_STR);
	$rlog->bindValue(':ip_remote', $_SERVER["REMOTE_ADDR"], PDO::PARAM_STR);
	$rlog->execute();
}

function GetSessions($token, $gdb){
	session_start();
	/*$qselect=$gdb->prepare("SELECT `ID`, `IDEMPLEADO`, `IDUSERGROUP` FROM `sessions_active` WHERE ((`TOKEN`=:token) AND (`IP_ADDRESS`=:ip_address) AND (`IP_REMOTE`=:ip_remote));");
	$qselect->bindValue(':token', $token, PDO::PARAM_STR);
	$qselect->bindValue(':ip_address', GetIPAddress(), PDO::PARAM_STR);
	$qselect->bindValue(':ip_remote', $_SERVER["REMOTE_ADDR"], PDO::PARAM_STR);
	$qselect->execute();
	$qresults=$qselect->fetchAll(PDO::FETCH_ASSOC);*/
	if (isset($_SESSION['token']) && ($_SESSION['token']==$token) && ($_SESSION["ip_address"]==GetIPAddress()) && ($_SESSION["ip_remote"]==$_SERVER["REMOTE_ADDR"])){
		$qresults[0]=$_SESSION;
	}else{
		$qresults=null;
	}
	print_r($qresults);
	if (count($qresults)<=0){
		$qselect=$gdb->prepare("SELECT * FROM `sessions` WHERE ((`TOKEN`=:token) AND (`IP_ADDRESS`=:ip_address) AND (`IP_REMOTE`=:ip_remote));");
		$qselect->bindValue(':token', $token, PDO::PARAM_STR);
		$qselect->bindValue(':ip_address', GetIPAddress(), PDO::PARAM_STR);
		$qselect->bindValue(':ip_remote', $_SERVER["REMOTE_ADDR"], PDO::PARAM_STR);
		$qselect->execute();
		$qresults=$qselect->fetchAll(PDO::FETCH_ASSOC);	
		if (count($qresults)>0){
			$_SESSION["TOKEN"]=$qresults[0]["TOKEN"];
			$_SESSION["IDEMPLEADO"]=$qresults[0]["IDEMPLEADO"];
			$_SESSION["IDUSERGROUP"]=$qresults[0]["IDUSERGROUP"];
			$_SESSION["ip_address"]=$qresults[0]["IP_ADDRESS"];
			$_SESSION["ip_remote"]=$qresults[0]["IP_REMOTE"];
			$_SESSION["life"]=$qresults[0]["LIFE"];
			$rsessions=$gdb->prepare("INSERT INTO `sessions_active` (`ID`, `TOKEN`, `IDEMPLEADO`, `IDUSERGROUP`, `IP_ADDRESS`, `IP_REMOTE`, `WHEN`, `LIFE`) SELECT `ID`, `TOKEN`, `IDEMPLEADO`, `IDUSERGROUP`, `IP_ADDRESS`, `IP_REMOTE`, `WHEN`, `LIFE` FROM SESSIONS WHERE ((`ID`=:ID));");
			$rsessions->bindValue(':ID', $qresults[0]['ID'], PDO::PARAM_INT);
			$rsessions->execute();
			unset($qresults[0]['ID']);
		}
	}
	if (count($qresults)>0){
		if (True) { // Pendiente Validar Tiempo de Session
			return $qresults[0];
		}else{
			return Null;
		}
	}else{
		return Null;
	}
}

function LastLog($IDEMPLEADO, $gdb){
	$qselect=$gdb->prepare("SELECT DATE(`WHEN`) AS `LASTLOG` FROM `log_requests` WHERE ((`IDEMPLEADO`)= :IDEMPLEADO) ORDER BY `id` DESC LIMIT 1;");
	$qselect->bindValue(':IDEMPLEADO', $IDEMPLEADO, PDO::PARAM_INT);
	$qselect->execute();
	$qresults=$qselect->fetchAll(PDO::FETCH_ASSOC);
	if (count($qresults)>0){
		return $qresults[0]["LASTLOG"];
	}else{
		return Null;
	}
}

function GetUserEmail($IDEMPLEADO, $gdb){
	$qselect=$gdb->prepare("SELECT `CORREO` FROM `empleados` WHERE ((`IDEMPLEADO`)= :IDEMPLEADO);");
	$qselect->bindValue(':IDEMPLEADO', $IDEMPLEADO, PDO::PARAM_INT);
	$qselect->execute();
	$qresults=$qselect->fetchAll(PDO::FETCH_ASSOC);
	if (count($qresults)>0){
		return $qresults[0]["CORREO"];
	}else{
		return Null;
	}
}
?>