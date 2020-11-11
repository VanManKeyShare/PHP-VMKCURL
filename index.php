<?php
include("vmkCurl.php");
if(PHP_OS!="WINNT"){$curdir = getcwd().'/';}else{$curdir = getcwd().'\\';};

$user = 'user@domain.com';
$pass = 'password';

$curl = new vmkCurl();
$curl->url = "https://abc.com/login.php";
$curl->source = "https://abc.com/index.php";
$curl->browser = "Mozilla/5.0 (Windows NT 6.2; rv:24.0) Gecko/20100101 Firefox/24.0";
$curl->cookie = $curdir."cookies.ini";
if(!file_exists($curl->cookie)){
	$File = fopen($curl->cookie,'w');
	fclose($File);
}
$curl->headers = array(
	'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*'.'/*;q=0.8',
	'Accept-Language: en-us,en;q=0.5',
	'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7',
	'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
	'Connection: close'
);
$curl->du_lieu_can_send = array(
	'email'=>$user,
	'password'=>$pass
);
$dulieu = $curl->post();
$dulieu = $dulieu['body'];

if($dulieu!=""){
	if(strpos($dulieu,'<h2>Sign in Success</h2>') !== false){
		echo 'Login Success';
		$curl = new vmkCurl();
		$curl->url = "https://abc.com/index.php";
		$curl->source = "https://abc.com/login.php";
		$curl->browser = "Mozilla/5.0 (Windows NT 5.1; rv:9.0.1) Gecko/20100101 Firefox/9.0.1";
		$curl->cookie = $curdir."cookies.ini";
		if(!file_exists($curl->cookie)){
			$File = fopen($curl->cookie,'w');
			fclose($File);
		}
		$curl->headers = array(
			'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*'.'/*;q=0.8',
			'Accept-Language: en-us,en;q=0.5',
			'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7',
			'Connection: close'
		);
		$dulieu = $curl->get();
		$dulieu = $dulieu['body'];
	}else if(strpos($dulieu,'<span>Invalid login</span>')!==false){
		echo 'Login False';
	}
}
?>
