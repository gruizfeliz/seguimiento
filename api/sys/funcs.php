<?php
//Search a Word in a String from an Array
function SContains($string, Array $search, $CaseSensitive = False) {
    $search_exp='/'.implode('|',array_map('preg_quote',$search)).($CaseSensitive ? '/' : '/i');
    return preg_match($search_exp, $string) ? True : false;
}

//Format the Date Format String
function DateFormatString($string) {
	//$separator=$string[0];
	//$string[0]=" ";
	$string=strtolower(trim($string));
	foreach (Array("m" => "mm","d" => "dd","y" => "yy") as $key => $value){
		while (strpos($string,$value)!==false){
			$string=str_replace($value, $key, $string);
		}
	}
	//return str_replace(Array("m","d","y","\/","/"), Array("%m","%d","%Y",$separator,$separator), $string);
	return str_replace(Array("m","d","y","\/"), Array("%m","%d","%Y","/"), $string);
}

//Format a Search String for Like Expressions
function FormatSearchS($string) {
    /*if (substr_count($string, '*')>0){
		return str_replace('*', '%', $string);
	}elseif (substr_count($string, '_')>0){
		return $string;
	}else{
		return "%".$string."%";
	}*/
	$string=trim($string);
	if (($string=='') || ($string=='*')) {
		return '.*';
	}else{
		return '('.str_replace(Array(', ',',',' '), Array('.*)(','.*)(','.*)('), $string).'.*)';
	}
}

//Recursive Encode Arrays Strings To Safe UTF8
function Utf8EncodeRecursive($array){
	$result = array();
	foreach ($array as $key => $value){
		if (is_array($value)){
			$result[$key] = Utf8EncodeRecursive($value);
		}elseif (is_string($value)){
			$result[$key] = utf8_encode($value);
		}else{
			$result[$key] = $value;
		}
	}
	return $result;
}

function EmailMessageID(){
	return sprintf("<%s.%s@%s>",
					base_convert(microtime(), 10, 36),
					base_convert(bin2hex(mcrypt_create_iv(8, MCRYPT_DEV_URANDOM)), 16, 36),
					$_SERVER['SERVER_NAME']);
}

//Templated Email
function TemplatedEmail($sendto, $subject, $email, $bidings, $headers) {
	$email=str_replace(array_keys($bidings), array_values($bidings), $email);
	return mail($sendto, $subject, $email, implode("\r\n", $headers));
}

//Backwards Compatibility PHP<5.4
if (!function_exists('http_response_code')){
    function http_response_code($newcode = NULL){
        static $code = 200;
        if($newcode !== NULL){
            header('X-PHP-Response-Code: '.$newcode, true, $newcode);
            if(!headers_sent()){
                $code = $newcode;
			}
        }       
        return $code;
    }
}

if (!function_exists('mcrypt_create_iv')){
	function make_seed(){
		list($usec, $sec) = explode(' ', microtime());
		return (float) $sec + ((float) $usec * 100000);
	}
	function mcrypt_create_iv($size, $source){
		for ($c = 1; $c <= $size; $c++) {
			srand(make_seed());
			$result.=chr(rand(32,126));
		}
		return $result;
		
	}
}

?>