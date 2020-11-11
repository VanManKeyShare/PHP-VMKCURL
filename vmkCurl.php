<?php
/*****************************************

	vmkCurl v1.3

	# Example GET

	include("vmkCurl.php");
	if(PHP_OS!="WINNT"){$curdir = getcwd().'/';}else{$curdir = getcwd().'\\';}
	$curl = new vmkCurl();
	$curl->url = "http://yahoo.com";
	$curl->source = "http://www.google.com";
	$curl->browser = "Mozilla/5.0 (Windows NT 5.1; rv:9.0.1) Gecko/20100101 Firefox/9.0.1";
	$curl->cookie = $curdir."cookies.ini";
	// if(!file_exists($curl->cookie)){
		// $File = fopen($curl->cookie,'w');
		// fclose($File);
	// }
	$curl->headers = array(
		'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*'.'/*;q=0.8',
		'Accept-Language: en-us,en;q=0.5',
		'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7',
		'Connection: close'
	);

	print_r($curl->get());

	# Example POST

	include("vmkCurl.php");
	if(PHP_OS!="WINNT"){$curdir = getcwd().'/';}else{$curdir = getcwd().'\\';};
	$curl = new vmkCurl();
	$curl->url = "http://yahoo.com";
	$curl->source = "http://www.google.com";
	$curl->browser = "Mozilla/5.0 (Windows NT 5.1; rv:9.0.1) Gecko/20100101 Firefox/9.0.1";
	$curl->cookie = $curdir."cookies.ini";
	// if(!file_exists($curl->cookie)){
		// $File = fopen($curl->cookie,'w');
		// fclose($File);
	// }
	$curl->headers = array(
		'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*'.'/*;q=0.8',
		'Accept-Language: en-us,en;q=0.5',
		'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7',
		'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
		'Connection: close'
	);
	$curl->du_lieu_can_send = array(
		'Username'=>'user@domain.com',
		'Password'=>'123456'
	);

	print_r($curl->post());

*****************************************/

class vmkCurl{
	private $ch = "";
	private $pp = "GET";

	public $url = "";
	public $source = "";
	public $browser = "Mozilla/5.0 (Windows NT 5.1; rv:9.0.1) Gecko/20100101 Firefox/9.0.1";
	public $cookie = "";
	public $TimeOUT = 60;
	public $show_header = true;
	public $show_body = true;
	public $allow_redir = true;
	public $max_redir = 10;
	public $headers = array();
	public $du_lieu_can_send = array();
	public $more_curl_ops = array();

	function __construct(){
		if (!function_exists('curl_init')){
			die('CURL IS NOT INSTALLED!');
		}else{
			$this->ch = curl_init();
		}
	}
	function get(){
		$ch = $this->ch;
		$this->pp = "GET";
		if(is_array($this->du_lieu_can_send) && count($this->du_lieu_can_send)!=0){
			if(strpos($this->url,"?")){
				$this->url = substr($this->url,0,strpos($this->url,"?"));
			}
			$this->du_lieu_can_send = http_build_query($this->du_lieu_can_send);
			$this->url.="?".$this->du_lieu_can_send;
		}
		$this->ch = $ch;
		return $this->request();
	}
	function post(){
		$ch = $this->ch;
		$this->pp = "POST";
		if(is_array($this->du_lieu_can_send) && count($this->du_lieu_can_send)!=0){
			$du_lieu_can_send1 = "";
			foreach ($this->du_lieu_can_send as $key => $value){
				$du_lieu_can_send1 = $du_lieu_can_send1.$key."=".urlencode($value)."&";
			}
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $du_lieu_can_send1);
		}
		$this->ch = $ch;
		return $this->request();
	}
	function upload($zilename, $buffersize = 0, $callback = ""){
		$ch = $this->ch;
		$this->pp = "UPLOAD";
		if(is_array($this->du_lieu_can_send) && count($this->du_lieu_can_send)!=0){
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $this->du_lieu_can_send);
			if($callback!=""){
				curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, $callback);
				curl_setopt($ch, CURLOPT_NOPROGRESS, false);
			}
			if($buffersize!=0 && intval($buffersize)>0){
				curl_setopt($ch, CURLOPT_BUFFERSIZE, $buffersize);
			}
		}else{
			return array('header'=>'','body'=>'','error'=>'ERROR: DU_LIEU_CAN_SEND NOT ARRAY');
		}
		array_push($this->headers,'Expect: ');
		$this->ch = $ch;
		return $this->request();
	}
	private function request(){
		ob_start();
		
		$ch = $this->ch;
		$pp = $this->pp;
		
		$url = $this->url;
		$source = $this->source;
		$cookie = $this->cookie;
		$browser = $this->browser;
		$TimeOUT = intval($this->TimeOUT);
		$show_header = $this->show_header;
		$show_body = $this->show_body;
		$allow_redir = $this->allow_redir;
		$max_redir = intval($this->max_redir);
		$headers = $this->headers;
		$du_lieu_can_send = $this->du_lieu_can_send;
		$more_curl_ops = $this->more_curl_ops;
		
		/******/

		if($url!=""){
			$parseURL = parse_url($url);
			if(empty($parseURL['scheme'])){return array('header'=>'','body'=>'','error'=>'ERROR: SCHEME NOT FOUND IN URL');}
			if(empty($parseURL['host'])){return array('header'=>'','body'=>'','error'=>'ERROR: HOST NOT FOUND IN URL');}
			curl_setopt($ch, CURLOPT_URL, $url);
		}else{return array('header'=>'','body'=>'','error'=>'ERROR: URL IS EMPTY');}
		if($browser!=""){
			curl_setopt($ch, CURLOPT_USERAGENT, $browser);
		}
		if($cookie!=""){
			if (!file_exists($cookie)){return array('header'=>'','body'=>'','error'=>'ERROR: COOKIE FILE NOT FOUND');}else{
				curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
				curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
			}
		}else{return array('header'=>'','body'=>'','error'=>'ERROR: COOKIE FILE IS EMPTY');}
		if($source!=""){
			curl_setopt($ch, CURLOPT_REFERER, $source);
		}
		if(is_array($headers) && count($headers)>0){
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		}
		if(is_bool($show_header)){
			curl_setopt($ch, CURLOPT_HEADER, $show_header);
		}
		if(is_bool($show_body)){
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, $show_body);
		}
		if($TimeOUT > 0){
			curl_setopt($ch, CURLOPT_TIMEOUT, $TimeOUT);
		}
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		if(is_array($more_curl_ops)==1 && count($more_curl_ops)!=0){
			foreach($more_curl_ops as $keyz => $valuez){
				curl_setopt($ch,constant(strtoupper($keyz)),$valuez);
			}
		}
		
		$output = "";
		if(is_bool($allow_redir) && $allow_redir == true && $max_redir>0){
			if(!ini_get('safe_mode') && !ini_get('open_basedir')){
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				curl_setopt($ch, CURLOPT_MAXREDIRS, $max_redir);
				$output = curl_exec($ch);
			}else{
				$scheme = strtolower($parseURL['scheme']);
				if(in_array($scheme,array('http','https'))){
					$output = $this->curl_redir_exec($ch,1,$max_redir);
				}else{
					$output = curl_exec($ch);
				}
			}
		}else{
			$output = curl_exec($ch);
		}

		/******/

		$errors = curl_error($ch);
		curl_close($ch);
		ob_end_clean();

		$header = "";
		$body = "";
		
		if(is_bool($show_body) && $show_body == true){
			if(is_bool($show_header) && $show_header == true){
				$separ = 0;
				$separ = strpos($output,"\r\n\r\n");
				$header = substr($output,0,$separ);
				$body = substr($output,$separ);
			}else{
				$header = "";
				$body = $output;
			}
		}

		return array(
			'header' => $header,
			'body' => $body,
			'error' => $errors
		);
	}
	private function curl_redir_exec($ch, $num_redir, $num_max_redir){
		$ch_copy = curl_copy_handle($ch);

		$curl_ops1 = curl_getinfo($ch_copy);
		$url = $curl_ops1['url'];

		$output = curl_exec($ch_copy);
		if($num_redir >= $num_max_redir){return $output;}
		$http_code = curl_getinfo($ch_copy, CURLINFO_HTTP_CODE);
		if($http_code == 301 || $http_code == 302){
			$separ = strpos($output,"\r\n\r\n");
			$header = substr($output,0,$separ);
			preg_match('/(Location:|URI:)(.*?)\n/', $header, $url_new);
			$url_new = trim($url_new[1]);
			if(!empty($url_new)){
				$parseURL2 = parse_url($url_new);
				if(!empty($parseURL2['scheme']) && !empty($parseURL2['host'])){
					curl_setopt($ch_copy, CURLOPT_URL, $url_new);
				}else{
					$urlok="";$scheme="";$user="";$pass="";$host="";$port="";$path="";$query="";$fragment="";
					$parseUrl = parse_url($url);
					if(!empty($parseUrl['scheme'])){$scheme = $parseUrl['scheme']."://";}
					if(!empty($parseUrl['user'])){$user = $parseUrl['user'].":";}
					if(!empty($parseUrl['pass'])){$pass = $parseUrl['pass']."@";}
					if(!empty($parseUrl['host'])){$host = $parseUrl['host'];}
					if(!empty($parseUrl['port'])){$port = ":".$parseUrl['port'];}
					if(!empty($parseUrl['path'])){$path = $parseUrl['path'];}
					if(!empty($parseUrl['query'])){$query = "?".$parseUrl['query'];}
					if(!empty($parseUrl['fragment'])){$fragment = "#".$parseUrl['fragment'];}
					$dir = $url_new;
					while(strpos($dir,"/./")!== false){$dir = str_replace("/./","/",$dir);}
					while(strpos($dir,"//")!== false){$dir = str_replace("//","/",$dir);}
					if(empty($path) || $path=="/"){
						while(substr($dir,0,3)=="../" || substr($dir,0,2)=="./"){
							if(substr($dir,0,3)=="../"){$dir = substr($dir,3);}
							if(substr($dir,0,2)=="./"){$dir = substr($dir,2);}
						}
						while(strpos($dir,"../../")!== false){$dir = str_replace("../../","",$dir);}
						while(substr($dir,0,4)=="/../"){
							if(substr($dir,0,4)=="/../"){$dir = substr($dir,4);}
						}
					}else{
						while(strpos($dir,"../../")!== false){$dir = str_replace("../../","",$dir);}
					}
					$path = substr($path,0,strrpos($path,"/"))."/".$dir;
					while(strpos($path,"/./")!== false){$path = str_replace("/./","/",$path);}
					while(strpos($path,"//")!== false){$path = str_replace("//","/",$path);}
					$urlok = $scheme.$user.$pass.$host.$port.$path.$query.$fragment;
					curl_setopt($ch_copy, CURLOPT_URL, $urlok);
				}
				$curl_ops3 = curl_getinfo($ch_copy);
				$parseURL3 = parse_url($curl_ops3['url']);
				$scheme3 = strtolower($parseURL3['scheme']);
				if(in_array($scheme3,array('http','https')) && !empty($parseURL3['host'])){
					$num_redir++;
					return $this->curl_redir_exec($ch_copy, $num_redir, $num_max_redir);
				}
			}
		}
		return $output;
	}
	function __destruct(){
		if(gettype($this->ch)=="resource"){
			curl_close($this->ch);
		}
	}
}
?>
