<!-- Warning this page Charset must be UTF-8-->
<?php
 
	require("usablewebLib.php");

$pap_apikey='myapikey';	  //place your apikey here
 
 

 
$thedomain=trim($_REQUEST["domainName"]);
	 
	$pieces = explode(".", $thedomain);
	$countarray=count($pieces);
	
	$temp=$countarray;
	$myext="";
	while($temp>0){
		if($myext!=""){
			$myext=$pieces[$temp].".".$myext;
		}else{
			$myext=$pieces[$temp] ;
		}
			$temp=$temp-1;
	}
	 
	$search = new PapakiDomainNameSearch($pieces[0]);
	$search->apikey = $pap_apikey;  
	 
	$search->use_curl = true;
 
	$search->extensions = ".".$myext;
	 

	$search->exec_request_for(_TYPE_DS,true);
	

if (count($search->arrayAvDomains) != 0)
	{
		for($i=0;$i < count($search->arrayAvDomains);$i++)
		{ 
			 
			   echo $search->arrayAvDomains[$i]."<br>"  ; 
				if(trim($search->arrayAvDomains[$i])==trim($_REQUEST["domainName"])){
					echo "Domain is not registered";	
				}
				 
  				 
 		 	  
  		}
	}
	
	else{
					 
					$search2 = new PapakiDomainNameSearch($_REQUEST["domainName"]);
					$search2->apikey = $pap_apikey;  
					 
					$search2->use_curl = true;
				
					$search2->exec_request_for(_TYPE_WHOIS);
?>
					<?=$search2->whois_response;?>	 <?
	}
	
	 function myEndsWith($Haystack, $Needle){
    // Recommended version, using strpos
    return strrpos($Haystack, $Needle) === strlen($Haystack)-strlen($Needle);
}
?>


