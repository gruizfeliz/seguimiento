<?php
//Select
function SysDBSelect($rparams, $gdb) {
	$api2=isset($rparams["api"][1])? intval($rparams["api"][1]) : 0;
	$tstructure=isset($GLOBALS["tstructure"]) ? $GLOBALS["tstructure"] : GetTableStructure(1,$rparams["t"], $gdb);
	$sqselect=isset($GLOBALS["sqselect"]) ? $GLOBALS["sqselect"] : $tstructure[2];
	$sqselect_c=isset($GLOBALS["sqselect_c"]) ? $GLOBALS["sqselect_c"] : $tstructure[21];
	$sqwhere=isset($GLOBALS["sqwhere"]) ? $GLOBALS["sqwhere"] : Null;
	$sqorder=isset($GLOBALS["sqorder"]) ? $GLOBALS["sqorder"] : "";
	$squote=isset($GLOBALS["squote"]) ? $GLOBALS["squote"] : Null;
	if (isset($rparams["aliaslist"])){
		$aliaslist=$rparams["aliaslist"];
		unset($rparams["aliaslist"]);
	}else{
		$aliaslist=Null;
	}
	//var_dump($GLOBALS);
	if (isset($rparams["p"])){
		$sqselect_limit=" LIMIT ".(($rparams["p"] - 1) * $rparams["np"]).",".$rparams["np"];
	}else{
		$sqselect_limit="";
	}
	//var_dump($tstructure);
	if ($api2==0){
		$qselect_c=$gdb->prepare($sqselect_c.$tstructure[6].";");
		$qselect=$gdb->prepare($sqselect.$tstructure[6].$sqorder.$sqselect_limit.";");
		$condition=$tstructure[7];
		foreach ($tstructure[1] as $tf_key => $table_field) {
			if ($condition==1){
				if ($table_field["Usable"]==0){
					$qselect->bindValue(':'.$table_field["Field"], isset($rparams[$table_field["Field"]]) ? $rparams[$table_field["Field"]] : 0, $table_field["PDOType"]);
					$qselect_c->bindValue(':'.$table_field["Field"], isset($rparams[$table_field["Field"]]) ? $rparams[$table_field["Field"]] : 0, $table_field["PDOType"]);
					break;
				}
			}else{
				$qselect->bindValue(':'.$table_field["Field"], isset($rparams[$table_field["Field"]]) ? $rparams[$table_field["Field"]] : $table_field["Value"], $table_field["PDOType"]);
				$qselect_c->bindValue(':'.$table_field["Field"], isset($rparams[$table_field["Field"]]) ? $rparams[$table_field["Field"]] : $table_field["Value"], $table_field["PDOType"]);
			}
		}
	}else{
		if (!isset($sqwhere)){
			$sqwhere=" WHERE (";
			foreach ($tstructure[1] as $tf_key => $table_field) {
				if (isset($rparams[$table_field["Field"]])){
					if($table_field["PDOType"]==PDO::PARAM_STR){
						$sqwhere.="((".$table_field["Field"].") Like :".$table_field["Field"].") OR ";
					}else{
						$sqwhere.="((".$table_field["Field"].")= :".$table_field["Field"].") OR ";
					}
				}
			}
			if ($sqwhere!=" WHERE ("){
				$sqwhere=rtrim($sqwhere, " OR ").")";
			}else{
				$sqwhere="";
			}
		}
		if (!isset($squote)){
			$squote="%";
		}
		$qselect=$gdb->prepare($sqselect.$sqwhere.$sqorder.$sqselect_limit.";");
		if (substr_count($sqselect_c, "SELECT")==1){
			$qselect_c=$gdb->prepare($sqselect_c.$sqwhere.";");
		}else{
			$qselect_c=$gdb->prepare($sqselect_c.";");
		}
		//print $tstructure[2].$sqwhere.$sqselect_limit.";";
		foreach ($tstructure[1] as $tf_key => $table_field) {
			if (isset($rparams[$table_field["Field"]])){
				//print "<br />".$table_field["Field"];
				if($table_field["PDOType"]==PDO::PARAM_STR){
					$qselect->bindValue(':'.$table_field["Field"], $squote.$rparams[$table_field["Field"]].$squote, $table_field["PDOType"]);
					$qselect_c->bindValue(':'.$table_field["Field"], $squote.$rparams[$table_field["Field"]].$squote, $table_field["PDOType"]);
					//print "%".$rparams[$table_field["Field"]]."%";
				}else{
					$qselect->bindValue(':'.$table_field["Field"], $rparams[$table_field["Field"]], $table_field["PDOType"]);
					$qselect_c->bindValue(':'.$table_field["Field"], $rparams[$table_field["Field"]], $table_field["PDOType"]);
					//print "#".$rparams[$table_field["Field"]]."#";
				}
			}
		}		
	}
	//var_dump($rparams);
	$qselect->execute();
	$qselect_c->execute();
	
	//$qselect->debugDumpParams();
	//print_r($qselect->errorCode());
	//print_r($qselect->errorInfo());
	//$qselect_c->debugDumpParams();
	//print_r($qselect_c->errorCode());
	//print_r($qselect_c->errorInfo());
	
	$qresults=$qselect->fetchAll(PDO::FETCH_ASSOC);
	$qresults_c=$qselect_c->fetchAll(PDO::FETCH_ASSOC);

	if (isset($aliaslist)){
		foreach ($aliaslist as $al_key => $alias) {
			$rparams[$alias] = $rparams[$al_key];
			unset($rparams[$al_key]);	
		}
	}
	
	//$rresponse["totalcount"]=isset($qresults_c[0]["RCOUNT"]) ? $qresults_c[0]["RCOUNT"] : 0;
	$rresponse["totalcount"]=isset($qresults_c[0]["RCOUNT"]) ? $qresults_c[0]["RCOUNT"] : 0;
	$rresponse["totalpages"]=isset($rparams["np"]) ? (($rparams["np"]<$rresponse["totalcount"]) ? ((($rresponse["totalcount"]-($rresponse["totalcount"] % $rparams["np"]))/$rparams["np"])+((($rresponse["totalcount"] % $rparams["np"])>0) ? 1 : 0)) : 1) : 1;
	$rresponse["page"]=isset($rparams["p"])? $rparams["p"] : 1;
	$rparams["p"]=($rresponse["page"]-1);
	$rresponse["prevpagelink"]=($rparams["p"]>0) ? $rparams : "";
	$rparams["p"]=($rresponse["page"]+1);
	$rresponse["nextpagelink"]=($rparams["p"]<=($rresponse["totalpages"])) ? $rparams : "";
	$rresponse["results"]=Null;
	$rresponse["status"]="Success";

	if (count($qresults)>0){
		if (count($qresults)>1){
			$rresponse["results"]=$qresults;
		}else{
			if ($api2==0){
				$rresponse["results"]=$qresults[0];
			}else{
				$rresponse["results"]=array($qresults[0]);
			}
		}
	}else{
		//$rresponse["results"]=array(0 => $tstructure);
		$rresponse["results"]=$qresults;
	}
	$rresponse["results"]=Utf8EncodeRecursive($rresponse["results"]);
	//var_dump($rresponse);
	//return json_encode($rresponse);
	return $rresponse;
}

//Insert
function SysDBInsert($rparams, $gdb) {
	$api2=isset($rparams["api"][1])? intval($rparams["api"][1]) : 0;
	$tstructure=isset($GLOBALS["tstructure"]) ? $GLOBALS["tstructure"] : GetTableStructure(1,$rparams["t"], $gdb);
	$sqinsert=isset($GLOBALS["sqinsert"]) ? $GLOBALS["sqinsert"] : ($api2==0) ? $tstructure[3] : Null;

	if (($api2!=0) && (!isset($sqinsert))){
		$sqinsert="INSERT INTO `".$rparams["t"]."` (";
		$sqinsert2="VALUES (";	
		foreach ($tstructure[1] as $tf_key => $table_field) {
			if (isset($rparams[$table_field["Field"]])){
				if ($table_field["Usable"]==1){
					$sqinsert.="`".$table_field["Field"]."`, ";
					$sqinsert2.=" :".$table_field["Field"].", ";
				}
			}
		}		
		$sqinsert=rtrim($sqinsert, ", ");
		$sqinsert2=rtrim($sqinsert2, ", ");
		$sqinsert.=") ".$sqinsert2.")";
	}

	$qinsert=$gdb->prepare($sqinsert.";");
	foreach ($tstructure[1] as $tf_key => $table_field) {
		if (($api2==0) || (isset($rparams[$table_field["Field"]]))){
			if ($table_field["Usable"]==1){
				$qinsert->bindValue(':'.$table_field["Field"], isset($rparams[$table_field["Field"]]) ? $rparams[$table_field["Field"]] : $table_field["Value"], $table_field["PDOType"]);
			}
		}
	}
	$qinsert->execute();
	
	//$qinsert->debugDumpParams();
	//print_r($qinsert->errorCode());
	//print_r($qinsert->errorInfo());
	
	//return json_encode($rresponse, JSON_FORCE_OBJECT);	
	//$rresponse["status"]="Success";
	if (isset($tstructure[11])){
		//$rresponse[$id_name]=$gdb->lastInsertId();
		return array("status" => "Success", 'ID' => $gdb->lastInsertId());
	}else{
		return array("status" => "Success");
	}
}

//Update
function SysDBUpdate($rparams, $gdb) {
	$api2=isset($rparams["api"][1])? intval($rparams["api"][1]) : 0;
	$tstructure=isset($GLOBALS["tstructure"]) ? $GLOBALS["tstructure"] : GetTableStructure(1,$rparams["t"], $gdb);
	$squpdate=isset($GLOBALS["squpdate"]) ? $GLOBALS["squpdate"] : ($api2==0) ? $tstructure[4] : Null;
	$sqwhere=isset($GLOBALS["sqwhere"]) ? $GLOBALS["sqwhere"] : $tstructure[6];	
	
	if (($api2!=0) && (!isset($squpdate))){
		$squpdate="UPDATE `".$rparams["t"]."` SET ";
		foreach ($tstructure[1] as $tf_key => $table_field) {
			if (isset($rparams[$table_field["Field"]])){
				if ($table_field["Usable"]==1){
					$squpdate.="`".$table_field["Field"]."`= :".$table_field["Field"].", ";
				}
			}
		}		
		$squpdate=rtrim($squpdate, ", ");
	}
	$qupdate=$gdb->prepare($squpdate.$sqwhere.";");
	if ($api2==0){
		foreach ($tstructure[1] as $tf_key => $table_field) {
			$qupdate->bindValue(':'.$table_field["Field"], isset($rparams[$table_field["Field"]]) ? $rparams[$table_field["Field"]] : $table_field["Value"], $table_field["PDOType"]);
		}
	}else{
		foreach ($tstructure[1] as $tf_key => $table_field) {
			if (isset($rparams[$table_field["Field"]])){
				$qupdate->bindValue(':'.$table_field["Field"], isset($rparams[$table_field["Field"]]) ? $rparams[$table_field["Field"]] : $table_field["Value"], $table_field["PDOType"]);
			}
		}	
	}
	$qupdate->execute();
	
	//$qupdate->debugDumpParams();
	//print_r($qupdate->errorCode());
	//print_r($qupdate->errorInfo());
	
	//$rresponse["status"]="Success";
	//return json_encode($rresponse, JSON_FORCE_OBJECT);	
	if (isset($tstructure[11])){
		//$rresponse[$id_name]=$gdb->lastInsertId();
		return array("status" => "Success", 'ID' => $rparams[$tstructure[11]]);
	}else{
		return array("status" => "Success");
	}
}

//Delete
function SysDBDelete($rparams, $gdb) {
	$api2=isset($rparams["api"][1])? intval($rparams["api"][1]) : 0;
	$tstructure=isset($GLOBALS["tstructure"]) ? $GLOBALS["tstructure"] : GetTableStructure(1,$rparams["t"], $gdb);
	$qdelete=$gdb->prepare($tstructure[5].$tstructure[6].";");
	$condition=$tstructure[7];
	foreach ($tstructure[1] as $tf_key => $table_field) {
		if ($condition==1){
			if ($table_field["Usable"]==0){
				$qdelete->bindValue(':'.$table_field["Field"], $rparams[$table_field["Field"]], $table_field["PDOType"]);
				break;
			}
		}else{
			$qdelete->bindValue(':'.$table_field["Field"], $rparams[$table_field["Field"]], $table_field["PDOType"]);
		}
	}
	$qdelete->execute();
	$qdelete->debugDumpParams();
	//print_r($qdelete->errorCode());
	//print_r($qdelete->errorInfo());
	//$rresponse["status"]="Success";
	//return json_encode($rresponse, JSON_FORCE_OBJECT);	
	if (isset($tstructure[11])){
		return array("status" => "Success", 'ID' => $rparams[$tstructure[11]]);
	}else{
		return array("status" => "Success");
	}
}

//Get Table Structure and all needed normal querys and cache it;
function GetTableStructure($option, $table_name, $gdb) {

	/*$data_types=array(	PDO::PARAM_BOOL => array ("BOOL","BOOLEAN"),
						PDO::PARAM_NULL => array ("NULL"),
						PDO::PARAM_INT => array ("INT","TINYINT","SMALLINT","MEDIUMINT","BIGINT","FLOAT","DOUBLE","DECIMAL"),
						PDO::PARAM_STR => array ("CHAR","VARCHAR","TINYTEXT","TEXT","MEDIUMTEXT","LONGTEXT","ENUM","SET"),
						PDO::PARAM_LOB => array ("BLOB","MEDIUMBLOB","LONGBLOB"));
	*/
	$data_types=array(	PDO::PARAM_BOOL => array ("BOOL","BOOLEAN"),
						PDO::PARAM_NULL => array ("NULL"),
						PDO::PARAM_INT => array ("INT","FLOAT","DOUBLE","DECIMAL"),
						PDO::PARAM_STR => array ("CHAR","TEXT","ENUM","SET","DATE","YEAR","MONTH","TIME"),
						PDO::PARAM_LOB => array ("BLOB"));
	global $table_fields_cached;
	/*print "<pre>";
	print_r($data_types);
	print "</pre>";*/

	//$time_start = microtime(true);
	$cache_file = "./api/cache/".$table_name."_cache.php";
	if (file_exists ($cache_file)) {
		include_once $cache_file;
	}else{
		$table_fields_cached[0]=null;
	}

	//$table_fields_cached = json_decode(file_get_contents($cache_file), true);
	$tfquery=$gdb->prepare("SHOW TABLE STATUS where Name = '$table_name'");
	$tfquery->execute();
	$table_fields = $tfquery->fetchAll(PDO::FETCH_ASSOC);
	$Create_time=$table_fields[0]["Create_time"];
	if ($Create_time!=$table_fields_cached[0]){
		//print "<p>entro</p>";
		$tfquery = $gdb->prepare("SHOW COLUMNS FROM `$table_name`");
		$tfquery->execute();
		$table_fields = array(0 => $Create_time, 1 => $tfquery->fetchAll(PDO::FETCH_ASSOC));
		//Querys Creation
		//SELECT
		$rqselect="SELECT * FROM `$table_name`";
		//INSERT
		$rqinsert="INSERT INTO `$table_name` (";
		$rqinsert2="VALUES (";		
		//UPDATE
		$rqupdate="UPDATE `$table_name` SET ";
		//DELETE
		$rqdelete="DELETE FROM `$table_name`";
		//WHERE
		$rqwhere=" WHERE (";
		$rqwhere2="";
		foreach ($table_fields[1] as $tf_key => $table_field) {
			if ($table_field["Extra"]!="auto_increment"){
				$table_fields[1][$tf_key]["PDOType"]=PDO::PARAM_STR;
				$table_fields[1][$tf_key]["Value"]="";
				if (SContains($table_field["Type"],$data_types[PDO::PARAM_BOOL],False)){
					$table_fields[1][$tf_key]["PDOType"]=PDO::PARAM_BOOL;
					$table_fields[1][$tf_key]["Value"]=False;
				}elseif(SContains($table_field["Type"],$data_types[PDO::PARAM_NULL],False)){
					$table_fields[1][$tf_key]["PDOType"]=PDO::PARAM_NULL;
					$table_fields[1][$tf_key]["Value"]="Null";
				}elseif(SContains($table_field["Type"],$data_types[PDO::PARAM_INT],False)){
					$table_fields[1][$tf_key]["PDOType"]=PDO::PARAM_INT;
					$table_fields[1][$tf_key]["Value"]=0;
				}elseif(SContains($table_field["Type"],$data_types[PDO::PARAM_LOB],False)){
					$table_fields[1][$tf_key]["PDOType"]=PDO::PARAM_LOB;
					$table_fields[1][$tf_key]["Value"]="";
				} 
				if ($table_fields[1][$tf_key]["Default"]!=Null){
					$table_fields[1][$tf_key]["Value"]=$table_fields[1][$tf_key]["Default"];
				}
				$table_fields[1][$tf_key]["Usable"]=1;
				//Querys Creation
				//INSERT
				$rqinsert.="`".$table_field["Field"]."`, ";
				$rqinsert2.=" :".$table_field["Field"].", ";
				//UPDATE
				$rqupdate.="`".$table_field["Field"]."`= :".$table_field["Field"].", ";
				//WHERE
				$rqwhere2.="((`".$table_field["Field"]."`)= :".$table_field["Field"].") OR ";
			}else{
				$table_fields[1][$tf_key]["PDOType"]=PDO::PARAM_INT;
				$table_fields[1][$tf_key]["Usable"]=0;
				$table_fields[11]=$table_field["Field"];
				$rqwhere.="((`".$table_field["Field"]."`)= :".$table_field["Field"].")";
			}
		}
		//Querys Creation
		//SELECT
		$table_fields[2]=$rqselect;
		$table_fields[21]="SELECT COUNT(*) AS RCOUNT FROM `$table_name`";
		//INSERT
		$rqinsert=rtrim($rqinsert, ", ");
		$rqinsert2=rtrim($rqinsert2, ", ");
		$rqinsert.=") ";
		$rqinsert2.=")";
		$table_fields[3]=$rqinsert.$rqinsert2;
		//UPDATE
		$rqupdate=rtrim($rqupdate, ", ");
		$table_fields[4]=$rqupdate;
		//DELETE
		$table_fields[5]=$rqdelete;
		//WHERE
		if ($rqwhere==" WHERE ("){
			$table_fields[6]=$rqwhere.rtrim($rqwhere2, " OR ").")";
			$table_fields[7]=0;
		}else{
			$table_fields[6]=$rqwhere.")";
			$table_fields[7]=1;
		}
		file_put_contents($cache_file, "<?php $"."table_fields_cached = json_decode('".json_encode($table_fields)."',true); ?>");
	}else{
		//print "<p>NO entro</p>";
		$table_fields=$table_fields_cached;
	}
	if ($option==0){
		foreach ($table_fields[1] as $tf_key => $table_field) {
			if ($table_field["Usable"]==1){
				$rfields[$table_field["Field"]]=$table_field["Value"];
			}
		}
		return $rfields;
	}
	if ($option==1){
		return $table_fields;
	}
/*print "<pre>";
print_r($table_fields);
print "</pre>";

$time_end = microtime(true);
$execution_time = ($time_end - $time_start)/60;
echo '<b>Total Execution Time:</b> '.$execution_time.' Mins';
*/
}
?>