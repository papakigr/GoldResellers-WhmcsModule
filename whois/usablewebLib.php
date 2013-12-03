<?php
 
//require ('libs/HttpClient.class.php');
require ('libs/HttpClient.class.php');
require_once('json.php');
/****************************************************************************************
*              			   UsableWeb Domain Name Search				
*---------------------------------------------------------------------------------------*
* 			 This is a Domain Name Search provided by Usableweb.				 			
* Requirments										
* In order to work this class you need the following: 					
* - web server with PHP enabled	(Apache, IIS, Sambar etc)				
* - PHP4 >= 4.3.3 (it may works and with earlier versions but with no gurantee )	
* - DOM XML extension enabled								
* - CURL (Client URL) extension enabled [only if you have to use clientXML() method] by default it's not needed]					 											
*---------------------------------------------------------------------------------------*
* How it works (quick reference):							
* Declare the following properties as in the "reply.php"			 	
* $ClassName->checkBoxPrefix - Is the prefix that should be given at the extensions		
* checkBoxes ex: ext_gr (for .gr) or ext_com (for .com.gr). (default = ext_)  		
* $ClassName->password- Is the password that provided by usableweb for this service	
* $ClassName->username- Is the username that provided by usableweb for this service     
* (notice that the validation checks your server's IP address) 					 
* $ClassName->domainName - is the domain name we searching for.	 			
*---------------------------------------------------------------------------------------*
* Methods of the class:									
* $ClassName->buildRequestXML([request_type]) - returns the appropiate XMLstring - [request_type] optional default value = _TYPE_DS(constant for domainSearch)			
* $ClassName->grubResponse() - Send the XML and returns the response [default method]
* $ClassName->clientXML() - Send the XML and returns the response [on demand if $ClassName->use_curl = true; then u r using this method instead $ClassName->grubResponse()
* [0] $ClassName->arrayAvDomains - Contais the available domain names			
* [1] $ClassName->arrayNotAvDomains - Contains the not available domain names		
* [2] $ClassName->whois_response - the reply if u have to perform a whois search.
*---------------------------------------------------------------------------------------*
*	         		Thats All folks! Enjoy!					
*****************************************************************************************/

define("_TYPE_DS","domainSearch");
define("_TYPE_WHOIS","whois");
 

class PapakiDomainNameSearch{
	//Property declaration
	var $requestURL;
	var $checkBoxPrefix;
	var $password;
	var $username;
	var $apikey;
	var $lang;
	var $type;
	var $test;
	var $domainName;
	var $extensions; //seperated by commas.
	var $requestXML;
	var $responseXML;
	var $responsearray;
	
	var $version = '1.2-Active Net';
	
	var $use_curl; //There are two method for sending the request and grubing the response by default the class using 'grabResponse()' method if u want to usa 'clientXML()' method set this variable to true.
	
	var $arrayAvDomains;
	var $arrayNotAvDomains;
	var $whois_response;
		
	//Class constructor -- declaring default values
	//Takes arguments $domainName argument the other arguments are optional.
	function PapakiDomainNameSearch($domainName, $ext="ext_", $lang="el",$test="False"){
		//die($domainName);
		$this->use_curl = false;
		$this->requestURL ="https://api.papaki.gr/register_url2.aspx";
		$this->checkBoxPrefix = $ext;
		$this->lang = $lang;
		//$this->type = $type;
		$this->test = $test;
		$this->domainName = $domainName;
		$this->arrayAvDomains = array();
		$this->arrayNotAvDomains = array();
	}
	//Takes 2 optional arguments $type: is the type of the request, we want to perform - domain name search or whois search
	//$use_get_extenssions_func:(works only with $type = _TYPE_DS) boolean if we use the '$this->getExtensions()' function or passes the 
	//extension by the $this->extensions property 
	function exec_request_for($type = _TYPE_DS, $use_get_extenssions_func = true){
		$this->type = $type;
 
 
		 
		 	 
	$json = new Services_JSON();
	 
	$jsonarray=array("request"=>array("type"=>$type,"apiKey"=>encodetolatin($this->apikey),"username"=>'',"password"=>'',"domain"=>encodetolatin($this->domainName),"lang"=>'el',"extensions"=>array("ext"=>array($this->extensions))));
	 if($this->type == _TYPE_WHOIS){
		 $jsonarray=array("request"=>array("type"=>$type,"apiKey"=>encodetolatin($this->apikey),"username"=>'',"password"=>'',"domain"=>encodetolatin($this->domainName),"lang"=>'el'));
		 
	 }
	
	 
	$Xpost = $json->encode($jsonarray);
	$Xpost=latintogreek($Xpost);
	
  
	
	$headers = array('Content-type: application/x-www-form-urlencoded');
		 
		 
			
	$pageContents = HttpClient::quickPost($this->requestURL,  array(
    'message' => $Xpost
));	 
	
 
  $pageContents = $this->StripHTML($pageContents);
 
	$this->responsearray = $json->decode($pageContents);
	
	 
		$this->parseResponse(); 
		 
		
	}
		
	 
	function grabResponse(){
		$this->responseXML = "";
		$Xpost = $this->requestXML;
		$url = $this->requestURL . $Xpost;
		if(!$fp = fopen(trim($url),'r')){
			//mail("debug@papaki.gr","UDNS Error", $this->requestXML."\n".$this->responseXML,"From:info@papaki.gr");
		}
		while(!feof($fp)){
			$this->responseXML .= fread($fp, 1024);
		}
		
		$this->parseResponse();
	}
	
	function parseResponse($executed = true){
		 
		$codeNode =  $this->responsearray->response->code;
 
 
		 
		if($codeNode != 1000){
			$message = $this->responsearray->response->message;
			 
			exit();
		}
		
		if ($this->type == _TYPE_DS){
			 $avDomains = $this->responsearray->response->availableDomains;
			 
			$json = new Services_JSON();
			$tempresponsearray = $json->decode($this->responsearray->response->availableDomains);
		if($tempresponsearray->domain!=""){
			array_push($this->arrayAvDomains, $tempresponsearray->domain);
		}
		 
	 	 
			
			 
			 
		}elseif($this->type = _TYPE_WHOIS){
			 
			$body = $this->responsearray->response->whoisReply;
			
			$body = str_replace('&lt;![CDATA[','',$body);
			$body = str_replace('<![CDATA[','',$body);
			$body = str_replace(']]&gt;','',$body);
			$body = str_replace(']]>;','',$body);
			$body = str_replace('&lt;','<',$body);
			$body = str_replace('&gt;','>',$body);
			$body = str_replace(']]&gt;','',$body);
			$body = str_replace(']]>','',$body);
			$body = str_replace(']]','',$body);
			  
			
			 
			
			
			$this->whois_response = $body;
			
			return $this->whois_response;
		}
	}
	
	 
	
	function fix_spaces($str){
		$s= split('[/,\]',$str);
		//print_r($s);
		for($i=0;$i <=count($s) - 1;$i++){
			$return .= trim($s[$i]).",";
		}
		return substr($return, 0, strlen($return) - 1 );
	}
	
	function GrantAccess($pass){
		if ($pass == $this->password){
			return true;
		}else{
			return false;
		}
	}
	
	function StripHTML($str){
		$openTag = "false";
		for($i=0;$i < strlen($str); $i++){
			if (substr($str, $i, 1) == "<"){
				$openTag = "true";
			}elseif (substr($str, $i, 1) == ">"){
				$openTag = "false";
			}
			if ($openTag !== "true" && substr($str, $i, 1) !== ">")
				$return .= substr($str, $i, 1);
		}
		$return = str_replace("\n", "<br />", $return);
		return $return;
	}
}

function encodetolatin($mystring){
	   
	
	 $mystring=str_replace("Α","&Alpha;",$mystring);  
	 $mystring=str_replace("Β","&Beta;",$mystring);  
	 $mystring=str_replace("Γ","&Gamma;",$mystring);  
	 $mystring=str_replace("Δ","&Delta;",$mystring);  
	 $mystring=str_replace("Ε","&Epsilon;",$mystring);  
	 $mystring=str_replace("Ζ","&Zeta;",$mystring);  
	 $mystring=str_replace("Η","&Eta;",$mystring);  
	 $mystring=str_replace("Θ","&Theta;",$mystring);  
	 $mystring=str_replace("Ι","&Iota;",$mystring);  
	 $mystring=str_replace("Κ","&Kappa;",$mystring);  
	 $mystring=str_replace("Λ","&Lambda;",$mystring);  
	 $mystring=str_replace("Μ","&Mu;",$mystring);  
	 $mystring=str_replace("Ν","&Nu;",$mystring);  
	 $mystring=str_replace("Ξ","&Xi;",$mystring);  
	 $mystring=str_replace("Ο","&Omicron;",$mystring);  
	 $mystring=str_replace("Π","&Pi;",$mystring);  
	 $mystring=str_replace("Ρ","&Rho;",$mystring);  
	 $mystring=str_replace("Σ","&Sigma;",$mystring);  
	 $mystring=str_replace("Τ","&Tau;",$mystring);  
	 $mystring=str_replace("Υ","&Upsilon;",$mystring);  
	 $mystring=str_replace("Φ","&Phi;",$mystring);  
	 $mystring=str_replace("Χ","&Chi;",$mystring);  
	 $mystring=str_replace("Ψ","&Psi;",$mystring);  
	 $mystring=str_replace("Ω","&Omega;",$mystring);  
	 $mystring=str_replace("α","&alpha;",$mystring);  
	 $mystring=str_replace("β","&beta;",$mystring);  
	 $mystring=str_replace("γ","&gamma;",$mystring);  
	 $mystring=str_replace("δ","&delta;",$mystring);  
	 $mystring=str_replace("ε","&epsilon;",$mystring);  
	 $mystring=str_replace("ζ","&zeta;",$mystring);  
	 $mystring=str_replace("η","&eta;",$mystring);  
	$mystring=str_replace("θ","&theta;",$mystring);  
	$mystring=str_replace("ι","&iota;",$mystring);  
	$mystring=str_replace("κ","&kappa;",$mystring);  
	$mystring=str_replace("λ","&lambda;",$mystring);  
	$mystring=str_replace("μ","&mu;",$mystring);  
	$mystring=str_replace("ν","&nu;",$mystring);  
	$mystring=str_replace("ξ","&xi;",$mystring);  
	$mystring=str_replace("ο","&omicron;",$mystring);  
	$mystring=str_replace("π","&pi;",$mystring);  
	$mystring=str_replace("ρ","&rho;",$mystring);  
	$mystring=str_replace("σ","&sigma;",$mystring);  
	$mystring=str_replace("τ","&tau;",$mystring);  
	$mystring=str_replace("υ","&upsilon;",$mystring);  
	$mystring=str_replace("φ","&phi;",$mystring);  
	$mystring=str_replace("χ","&chi;",$mystring);  
	$mystring=str_replace("ψ","&psi;",$mystring);  
	$mystring=str_replace("ω","&omega;",$mystring);  
	$mystring=str_replace("ς","&sigmaf;",$mystring); 
	
	          
	              
	$mystring=str_replace("ά","&#940;",$mystring); 
	$mystring=str_replace("έ","&#941;",$mystring); 
	$mystring=str_replace("ώ","&#974;",$mystring); 
	$mystring=str_replace("ύ","&#973;",$mystring); 
	$mystring=str_replace("ί","&#943;",$mystring); 
	$mystring=str_replace("ό","&#972;",$mystring); 
	$mystring=str_replace("ή","&#942;",$mystring); 
	$mystring=str_replace("Ά","&#902;",$mystring); 
	$mystring=str_replace("Έ","&#904;",$mystring); 
	$mystring=str_replace("Ώ","&#911;",$mystring); 
	$mystring=str_replace("Ύ","&#910;",$mystring); 
	$mystring=str_replace("Ί","&#906;",$mystring); 
	$mystring=str_replace("Ό","&#908;",$mystring); 
	$mystring=str_replace("Ή","&#905;",$mystring); 
	
	$mystring=str_replace("ϊ","&#970;",$mystring); 
	$mystring=str_replace("ΐ","&#912;",$mystring); 
	$mystring=str_replace("ϋ","&#971;",$mystring); 
	$mystring=str_replace("ΰ","&#944;",$mystring); 
	return $mystring;
	}
	
	
	
	function latintogreek($mystring){
	   
	
	 $mystring=str_replace("&Alpha;","Α",$mystring);  
	 $mystring=str_replace("&Beta;","Β",$mystring);  
	 $mystring=str_replace("&Gamma;","Γ",$mystring);  
	 $mystring=str_replace("&Delta;","Δ",$mystring);  
	 $mystring=str_replace("&Epsilon;","Ε",$mystring);  
	 $mystring=str_replace("&Zeta;","Ζ",$mystring);  
	 $mystring=str_replace("&Eta;","Η",$mystring);  
	 $mystring=str_replace("&Theta;","Θ",$mystring);  
	 $mystring=str_replace("&Iota;","Ι",$mystring);  
	 $mystring=str_replace("&Kappa;","Κ",$mystring);  
	 $mystring=str_replace("&Lambda;","Λ",$mystring);  
	 $mystring=str_replace("&Mu;","Μ",$mystring);  
	 $mystring=str_replace("&Nu;","Ν",$mystring);  
	 $mystring=str_replace("&Xi;","Ξ",$mystring);  
	 $mystring=str_replace("&Omicron;","Ο",$mystring);  
	 $mystring=str_replace("&Pi;","Π",$mystring);  
	 $mystring=str_replace("&Rho;","Ρ",$mystring);  
	 $mystring=str_replace("&Sigma;","Σ",$mystring);  
	 $mystring=str_replace("&Tau;","Τ",$mystring);  
	 $mystring=str_replace("&Upsilon;","Υ",$mystring);  
	 $mystring=str_replace("&Phi;","Φ",$mystring);  
	 $mystring=str_replace("&Chi;","Χ",$mystring);  
	 $mystring=str_replace("&Psi;","Ψ",$mystring);  
	 $mystring=str_replace("&Omega;","Ω",$mystring);  
	 $mystring=str_replace("&alpha;","α",$mystring);  
	 $mystring=str_replace("&beta;","β",$mystring);  
	 $mystring=str_replace("&gamma;","γ",$mystring);  
	 $mystring=str_replace("&delta;","δ",$mystring);  
	 $mystring=str_replace("&epsilon;","ε",$mystring);  
	 $mystring=str_replace("&zeta;","ζ",$mystring);  
	 $mystring=str_replace("&eta;","η",$mystring);  
	$mystring=str_replace("&theta;","θ",$mystring);  
	$mystring=str_replace("&iota;","ι",$mystring);  
	$mystring=str_replace("&kappa;","κ",$mystring);  
	$mystring=str_replace("&lambda;","λ",$mystring);  
	$mystring=str_replace("&mu;","μ",$mystring);  
	$mystring=str_replace("&nu;","ν",$mystring);  
	$mystring=str_replace("&xi;","ξ",$mystring);  
	$mystring=str_replace("&omicron;","ο",$mystring);  
	$mystring=str_replace("&pi;","π",$mystring);  
	$mystring=str_replace("&rho;","ρ",$mystring);  
	$mystring=str_replace("&sigma;","σ",$mystring);  
	$mystring=str_replace("&tau;","τ",$mystring);  
	$mystring=str_replace("&upsilon;","υ",$mystring);  
	$mystring=str_replace("&phi;","φ",$mystring);  
	$mystring=str_replace("&chi;","χ",$mystring);  
	$mystring=str_replace("&psi;","ψ",$mystring);  
	$mystring=str_replace("&omega;","ω",$mystring);  
	$mystring=str_replace("&sigmaf;","ς",$mystring); 
	
	          
	              
	$mystring=str_replace("&#940;","ά",$mystring); 
	$mystring=str_replace("&#941;","έ",$mystring); 
	$mystring=str_replace("&#974;","ώ",$mystring); 
	$mystring=str_replace("&#973;","ύ",$mystring); 
	$mystring=str_replace("&#943;","ί",$mystring); 
	$mystring=str_replace("&#972;","ό",$mystring); 
	$mystring=str_replace("&#942;","ή",$mystring); 
	$mystring=str_replace("&#902;","Ά",$mystring); 
	$mystring=str_replace("&#904;","Έ",$mystring); 
	$mystring=str_replace("&#911;","Ώ",$mystring); 
	$mystring=str_replace("&#910;","Ύ",$mystring); 
	$mystring=str_replace("&#906;","Ί",$mystring); 
	$mystring=str_replace("&#908;","Ό",$mystring); 
	$mystring=str_replace("&#905;","Ή",$mystring); 
	
	$mystring=str_replace("&#970;","ϊ",$mystring); 
	$mystring=str_replace("&#912;","ΐ",$mystring); 
	$mystring=str_replace("&#971;","ϋ",$mystring); 
	$mystring=str_replace("&#944;","ΰ",$mystring); 
	return $mystring;
	}


//if ($_GET['debug'] == 'true' && $_GET['version'] == 'true'){
//	$udns = new PapakiDomainNameSearch('');
//	echo ('<b>Class version:</b> ' . $udns->version);
//}

?>
