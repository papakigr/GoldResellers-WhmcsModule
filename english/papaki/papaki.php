<?php
/*
 * Version 4.2.6
 * 16/10/2019
 */

set_time_limit(200);




require('libs/HttpClient.class.php');
require_once('json.php');


function papaki_getConfigArray()
{
	$configarray = array(
		"APIkey" => array("Type" => "text", "Size" => "100", "Description" => "Enter the apikey",),
		"PostUrl" => array(
			"Type" => "text",
			"Size" => "64",
			"Description" => "Enter https://api.papaki.com/register_url2.aspx",
		),
		"check24hours" => array(
			"FriendlyName" => "Prevent multiple domain renewal",
			"Type" => "yesno",
			"Description" => "Prevents multiple domain renewals in 24 hours",
		),
		"TestMode" => array("Type" => "no",),
		"Convert Punycode domains" => array("Type" => "no",),
	);

	return $configarray;
}

function papaki_GetNameservers($params)
{
	$values = array();
	$username = '';
	$password = '';
	$apikey = encodetolatin($params["APIkey"]);
	$tld = encodetolatin($params["tld"]);
	$sld = encodetolatin($params["sld"]);


	$json = new Services_JSON();
	$jsonarray = array(
		"request" => array(
			"do" => 'getnameservers',
			"username" => $username,
			"password" => $password,
			"apiKey" => $apikey,
			"domainname" => $sld . "." . $tld
		)
	);
	$Xpost = $json->encode($jsonarray);
	$Xpost = latintogreek($Xpost);


	$pageContents = HttpClient::quickPost($params["PostUrl"], array(
		'message' => $Xpost
	));


	$responsearray = $json->decode($pageContents);
	$codeNode = $responsearray->response->code;
	$message = $responsearray->response->message;


	$nameserver1 = $responsearray->response->ns1;
	$nameserver2 = $responsearray->response->ns2;
	$nameserver3 = $responsearray->response->ns3;
	$nameserver4 = $responsearray->response->ns4;


	if ($codeNode != "1000") {

		$values["error"] = 'Error: ' . $codeNode . ' - ' . $message;
	}


	$values["ns1"] = $nameserver1;
	$values["ns2"] = $nameserver2;
	$values["ns3"] = $nameserver3;
	$values["ns4"] = $nameserver4;

	return $values;
}

function papaki_SaveNameservers($params)
{
	$values = array();
	$username = '';
	$password = '';
	$apikey = encodetolatin($params["APIkey"]);
	$tld = encodetolatin($params["tld"]);
	$sld = encodetolatin($params["sld"]);
	$nameserver1 = $params["ns1"];
	$nameserver2 = $params["ns2"];
	$nameserver3 = $params["ns3"];
	$nameserver4 = $params["ns4"];

	$ip1 = "";
	$ip2 = "";
	$ip3 = "";
	$ip4 = "";
	if ($nameserver1 != "") {
		$ip1 = gethostbyname($nameserver1);
		if ($ip1 == $nameserver1) {
			$ip1 = "";
		}
	}
	if ($nameserver2 != "") {
		$ip2 = gethostbyname($nameserver2);
		if ($ip2 == $nameserver2) {
			$ip2 = "";
		}
	}
	if ($nameserver3 != "") {
		$ip3 = gethostbyname($nameserver3);
		if ($ip3 == $nameserver3) {
			$ip3 = "";
		}
	}
	if ($nameserver4 != "") {
		$ip4 = gethostbyname($nameserver4);
		if ($ip4 == $nameserver4) {
			$ip4 = "";
		}
	}

	$nsarray = array();
	$counting = 0;


	$nameserver1 = encodetolatin($params["ns1"]);
	$nameserver2 = encodetolatin($params["ns2"]);
	$nameserver3 = encodetolatin($params["ns3"]);
	$nameserver4 = encodetolatin($params["ns4"]);


	if ($ip1 != "") {


		$nsarray[$counting] = array("Name" => $nameserver1, "Ip" => $ip1);
		$counting = $counting + 1;
	}
	if ($ip2 != "") {

		$nsarray[$counting] = array("Name" => $nameserver2, "Ip" => $ip2);
		$counting = $counting + 1;
	}

	if ($ip3 != "") {
		$nsarray[$counting] = array("Name" => $nameserver3, "Ip" => $ip3);
		$counting = $counting + 1;
	}


	if ($ip4 != "") {
		$nsarray[$counting] = array("Name" => $nameserver4, "Ip" => $ip4);
		$counting = $counting + 1;
	}


	$json = new Services_JSON();
	$jsonarray = array(
		"request" => array(
			"do" => 'editnameservers',
			"username" => $username,
			"password" => $password,
			"apiKey" => $apikey,
			"domainname" => $sld . "." . $tld,
			"lang" => 'el',
			"NameServers" => array("v4" => $nsarray)
		)
	);
	$Xpost = $json->encode($jsonarray);
	$Xpost = latintogreek($Xpost);


	$pageContents = HttpClient::quickPost($params["PostUrl"], array(
		'message' => $Xpost
	));

	$responsearray = $json->decode($pageContents);
	$codeNode = $responsearray->response->code;
	$message = $responsearray->response->message;


	if ($codeNode != "1000") {

		$values["error"] = 'Error: ' . $codeNode . ' - ' . $message;
	}


	return $values;
}

function papaki_GetRegistrarLock($params)
{
	$values = array();
	$username = '';
	$password = '';
	$apikey = encodetolatin($params["APIkey"]);
	$tld = encodetolatin($params["tld"]);
	$sld = encodetolatin($params["sld"]);


	if (isgrdomain($sld . "." . $tld) or iseudomain($sld . "." . $tld)) {
		$lockstatus = "unlocked";

		return $lockstatus;
	}


	$json = new Services_JSON();
	$jsonarray = array(
		"request" => array(
			"do" => 'getregistrarlock',
			"username" => $username,
			"password" => $password,
			"apiKey" => $apikey,
			"domainname" => $sld . "." . $tld,
			"customer_language" => 'gr'
		)
	);
	$Xpost = $json->encode($jsonarray);
	$Xpost = latintogreek($Xpost);


	$pageContents = HttpClient::quickPost($params["PostUrl"], array(
		'message' => $Xpost
	));


	$responsearray = $json->decode($pageContents);
	$codeNode = $responsearray->response->code;
	$message = $responsearray->response->message;

	$lock = "0";


	if ($codeNode != "1000") {

		$values["error"] = 'Error: ' . $codeNode . ' - ' . $message;
	} else {
		$lock = $responsearray->response->lock_state;

	}


	if ($lock == "True") {
		$lockstatus = "locked";
	} else {
		$lockstatus = "unlocked";
	}

	return $lockstatus;
}

function papaki_SaveRegistrarLock($params)
{
	$values = array();
	$username = '';
	$password = '';
	$apikey = encodetolatin($params["APIkey"]);
	$tld = encodetolatin($params["tld"]);
	$sld = encodetolatin($params["sld"]);


	if (isgrdomain($sld . "." . $tld) or iseudomain($sld . "." . $tld)) {
		return $values;
	}

	if ($params["lockenabled"] == "locked") {
		$lockstatus = "enable";
	} else {
		$lockstatus = "disable";
	}


	$json = new Services_JSON();
	$jsonarray = array(
		"request" => array(
			"do" => 'reglock',
			"username" => $username,
			"password" => $password,
			"apiKey" => $apikey,
			"domainname" => $sld . "." . $tld,
			"customer_language" => 'gr',
			"locktype" => $lockstatus
		)
	);
	$Xpost = $json->encode($jsonarray);
	$Xpost = latintogreek($Xpost);

	$pageContents = HttpClient::quickPost($params["PostUrl"], array(
		'message' => $Xpost
	));

	$responsearray = $json->decode($pageContents);
	$codeNode = $responsearray->response->code;
	$message = $responsearray->response->message;


	if ($codeNode != "1000") {

		$values["error"] = 'Error: ' . $codeNode . ' - ' . $message;
	}


	return $values;
}


function papaki_GetEmailForwarding($params)
{ //den uposthrizetai apo to Api


}

function papaki_SaveEmailForwarding($params)
{ //den uposthrizetai apo to Api


}

function papaki_GetDNS($params)
{//den uposthrizetai apo to Api


}

function papaki_SaveDNS($params)
{//den uposthrizetai apo to Api


}

function papaki_registerdomain($params)
{

	$values = array();

	$username = '';
	$password = '';
	$apikey = encodetolatin($params["APIkey"]);
	$description_ar = $params["additionalfields"];
	$description = encodetolatin($description_ar["Description"]);
	if (trim($description) == '') {
		$description = ' ';
	}


	//extra attributes
	$LegalType = encodetolatin($description_ar["Legal Type"]);
	$tax_id = encodetolatin($description_ar["Tax ID"]);
    $extra_country = encodetolatin($params["countrycode"]);

	if ($LegalType == "Italian and foreign natural persons") {
		$LegalType = "1";
	}
	if ($LegalType == "Companies/one man companies") {
		$LegalType = "2";
	}
	if ($LegalType == "Freelance workers/professionals") {
		$LegalType = "3";
	}
	if ($LegalType == "non-profit organizations") {
		$LegalType = "4";
	}
	if ($LegalType == "public organizations") {
		$LegalType = "5";
	}
	if ($LegalType == "other subjects") {
		$LegalType = "6";
	}
	if ($LegalType == "non natural foreigners") {
		$LegalType = "7";
	}


    $CompanyTitle = encodetolatin($description_ar["Company Title"]);
    $entityType = encodetolatin($description_ar["Entity Type"]);

    //NATURAL PERSON
    if ($entityType == "INDIVIDUAL") {
        $naturalPerson = "True";
        $params["companyname"] = "";
    } elseif ($entityType == "COMPANY") {
        $naturalPerson = "False";
    } else {
        $naturalPerson = "True";
        if (trim($params["companyname"]) != "") {
            $naturalPerson = "False";
        }
    }

    //CITIZENSHIP
    $citizenship = encodetolatin($description_ar["Citizenship"]);
    if($naturalPerson=="False"){
        $citizenship="";
    }

	//end extra attributes

	$tld = encodetolatin($params["tld"]);
	$sld = encodetolatin($params["sld"]);

	$idprotection = encodetolatin($params["idprotection"]);
	if ($idprotection == "1") {
		$idprotection = "true";
	} else {
		$idprotection = "false";
	}

    $phonenumber = $params["phonenumber"];
    $phonenumber = strtr($phonenumber, array(" " => ""));
    if (!(startswith($phonenumber, "+")) and !(startswith($phonenumber, "00"))) {
        $phonenumber = "+" . $params['phonecc'] .".". $phonenumber;
    }

    if (isgrdomain($sld . "." . $tld)) {
    if(trim($params["companyname"])==""){
        $params["companyname"]=$params["fullname"];
    }
    }

	$json = new Services_JSON();
	$jsonarray = array(
		"request" => array(
			"do" => 'domainregister',
			"apiKey" => $apikey,
			"domainname" => $sld . "." . $tld,
			"description" => $description,
			"ip1" => ' ',
			"ip2" => ' ',
			"ip3" => ' ',
			"ip4" => ' ',
			"ns1" => encodetolatin($params["ns1"]),
			"ns2" => encodetolatin($params["ns2"]),
			"ns3" => encodetolatin($params["ns3"]),
			"ns4" => encodetolatin($params["ns4"]),
            "owner_fullname" => encodetolatin($params["fullname"]),
            "owner_CompanyName" => encodetolatin($params["companyname"]),
			"owner_firstname" => encodetolatin($params["firstname"]),
			"owner_lastname" => encodetolatin($params["lastname"]),
			"owner_email" => encodetolatin($params["email"]),
			"owner_address" => encodetolatin($params["address1"]),
			"owner_state" => encodetolatin($params["state"]),
			"owner_city" => encodetolatin($params["city"]),
			"owner_postcode" => encodetolatin($params["postcode"]),
            "owner_country" => encodetolatin($params["countrycode"]),
            "owner_phone" => encodetolatin($phonenumber),
            "owner_fax" => '',
			"owner_litepsd" => ' ',
            "owner_CompanyTitle" => $CompanyTitle,
			"regperiod" => $params["regperiod"],
			"idprotect" => $idprotection,
			"customer_language" => "gr",
			"extraattributes" => array(
				"entity_type" => $LegalType,
				"nationality_code" => $extra_country,
				"reg_code" => $tax_id
			)
		)
	);

    if (iseudomain( $sld . "." . $tld) ){
        $jsonarray["request"]["owner_naturalPerson"] =$naturalPerson;
        $jsonarray["request"]["owner_citizenship"] =$citizenship;

    }


	$Xpost = $json->encode($jsonarray);
	$Xpost = latintogreek($Xpost);

	$pageContents = HttpClient::quickPost($params["PostUrl"], array(
		'message' => $Xpost
	));


	$responsearray = $json->decode($pageContents);
	$codeNode = $responsearray->response->code;
	$message = $responsearray->response->message;


	if ($codeNode != "1000") {

		$values["error"] = 'Error: ' . $codeNode . ' - ' . $message;
	}

	return $values;
}

function papaki_TransferDomain($params)
{

	$values = array();


//////////////////////////////////////////////////////////////////////////////////////////////////


	$username = '';
	$password = '';
	$apikey = encodetolatin($params["APIkey"]);
	$tld = encodetolatin($params["tld"]);
	$sld = encodetolatin($params["sld"]);
    $transfersecret = encodetolatin($params["eppcode"]);
    $extraAttr = $params["additionalfields"];

    $phonenumber = $params["phonenumber"];
    $phonenumber = strtr($phonenumber, array(" " => ""));
    if (!(startswith($phonenumber, "+")) and !(startswith($phonenumber, "00"))) {
        $phonenumber = "+" . $params['phonecc'] .".". $phonenumber;
    }
    if (isgrdomain($sld . "." . $tld)) {
        if (trim($params["companyname"]) == "") {
            $params["companyname"] = $params["fullname"];
        }
    }

	# Registrant Details
    $RegistrantFullName = encodetolatin($params["fullname"]);
	$RegistrantFirstName = encodetolatin($params["firstname"]);
	$RegistrantLastName = encodetolatin($params["lastname"]);
    $RegistrantCompanyName = encodetolatin($params["companyname"]);
	$RegistrantAddress1 = encodetolatin($params["address1"]);
	$RegistrantCity = encodetolatin($params["city"]);
	$RegistrantStateProvince = encodetolatin($params["state"]);
	$RegistrantPostalCode = encodetolatin($params["postcode"]);
    $RegistrantCountry = encodetolatin($params["countrycode"]);
	$RegistrantEmailAddress = encodetolatin($params["email"]);
    $RegistrantPhone = encodetolatin($phonenumber);

    $entityType = encodetolatin($extraAttr["Entity Type"]);

    //NATURAL PERSON
    if ($entityType == "INDIVIDUAL") {
        $naturalPerson = "True";
        $params["companyname"] = "";
        $RegistrantCompanyName = "";
    } elseif ($entityType == "COMPANY") {
        $naturalPerson = "False";
    } else {
        $naturalPerson = "True";
        if (trim($params["companyname"]) != "") {
            $naturalPerson = "False";
        }
    }

    $citizenship = encodetolatin($extraAttr["Citizenship"]);
    if($naturalPerson=="False"){
        $citizenship="";
    }

	$json = new Services_JSON();

	if (!isgrdomain($sld . "." . $tld) ) {
		$jsonarray = array(
			"request" => array(
				"do" => 'changeregistrar',
				"apiKey" => $apikey,
				"domainname" => $sld . "." . $tld,
				"customer_language" => 'gr',
				"authcode" => $transfersecret,
				"RegistrantContact" => array(
					"firstname" => $RegistrantFirstName,
					"lastname" => $RegistrantLastName,
					"fullname" => $RegistrantFullName,
                    "CompanyName" => $RegistrantCompanyName,
					"email" => $RegistrantEmailAddress,
					"address" => $RegistrantAddress1,
					"state" => $RegistrantStateProvince,
					"city" => $RegistrantCity,
					"postcode" => $RegistrantPostalCode,
					"country" => $RegistrantCountry,
					"phone" => $RegistrantPhone,
                    "fax" => "",
                    "title" => ""

				)
			)
		);

        if (iseudomain( $sld . "." . $tld) ){
            $jsonarray["request"]["RegistrantContact"]["naturalPerson"] =$naturalPerson;
            $jsonarray["request"]["RegistrantContact"]["citizenship"] =$citizenship;

        }
	} else {
		$jsonarray = array(
			"request" => array(
				"do" => 'changeregistrar',
				"username" => $username,
				"password" => $password,
				"apiKey" => $apikey,
				"domainname" => $sld . "." . $tld,
				"customer_language" => 'gr',
				"authcode" => $transfersecret
			)
		);

	}
	$Xpost = $json->encode($jsonarray);
	$Xpost = latintogreek($Xpost);


	$pageContents = HttpClient::quickPost($params["PostUrl"], array(
		'message' => $Xpost
	));
	$responsearray = $json->decode($pageContents);
	$codeNode = $responsearray->response->code;
	$message = $responsearray->response->message;


	if ($codeNode != "1000") {
		$values["error"] = 'Error: ' . $codeNode . ' - ' . $message;
	}

	return $values;
}

function papaki_RenewDomain($params)
{
	$values = array();
	$username = '';
	$password = '';
	$apikey = encodetolatin($params["APIkey"]);
	$tld = encodetolatin($params["tld"]);
	$sld = encodetolatin($params["sld"]);
	$regperiod = $params["regperiod"];
	$idprotection = $params["idprotection"];
	if ($idprotection == "1") {
		$idprotection = "true";
	} else {
		$idprotection = "false";
	}

	if ($params["check24hours"] == "on") {
		$params["check24hours"] = "True";
	}


	$json = new Services_JSON();
	$jsonarray = array(
		"request" => array(
			"do" => 'domainupdate',
			"apiKey" => $apikey,
			"domainname" => $sld . "." . $tld,
			"customer_language" => 'gr',
			"regperiod" => $regperiod,
			"idprotect" => $idprotection,
			"check24hours" => $params["check24hours"]
		)
	);
	$Xpost = $json->encode($jsonarray);
	$Xpost = latintogreek($Xpost);


	$pageContents = HttpClient::quickPost($params["PostUrl"], array(
		'message' => $Xpost
	));

	$responsearray = $json->decode($pageContents);

	$codeNode = $responsearray->response->code;

	$message = $responsearray->response->message;


	if ($codeNode != "1000") {
		$values["error"] = 'Error: ' . $codeNode . ' - ' . $message;
	}

	return $values;

}


function papaki_GetContactDetails($params)
{

	$values = array();
	$username = '';
	$password = '';
	$apikey = encodetolatin($params["APIkey"]);
	$tld = encodetolatin($params["tld"]);
	$sld = encodetolatin($params["sld"]);

	$json = new Services_JSON();
	$jsonarray = array(
		"request" => array(
			"do" => 'getcontactdetails',
			"apiKey" => $apikey,
			"domainname" => $sld . "." . $tld
		)
	);
	$Xpost = $json->encode($jsonarray);
	$Xpost = latintogreek($Xpost);


	$pageContents = HttpClient::quickPost($params["PostUrl"], array(
		'message' => $Xpost
	));


	$responsearray = $json->decode($pageContents);
	$codeNode = $responsearray->response->code;
	$message = $responsearray->response->message;


	$firstname = $responsearray->response->registrantFirstName;
	$LastName = $responsearray->response->registrantLastname;
    $OrganizationName = $responsearray->response->registrantorg;
    $CompanyTitle = $responsearray->response->registrantCompanyTitle;
    $fullname = $responsearray->response->registrantFullname;
	$EmailAddress = $responsearray->response->registrantemail;
	$Address1 = $responsearray->response->registrantaddress1;
	$Address2 = $responsearray->response->registrantaddress2;
	$City = $responsearray->response->registrantcity;
	$StateProvince = $responsearray->response->registrantstate;
	$PostalCode = $responsearray->response->registrantpostcode;
	$Country = $responsearray->response->registrantcountry;
	$Phone = $responsearray->response->registrantphone;
	$Fax = $responsearray->response->registrantfax;
 //   $naturalPerson = $responsearray->response->registrantNaturalPerson;
    $citizenship = $responsearray->response->registrantCitizenship;

	$Adminfirstname = $responsearray->response->adminFirstName;
	$AdminLastName = $responsearray->response->adminLastname;
    $AdminOrganizationName = $responsearray->response->adminorg;

	$AdminEmailAddress = $responsearray->response->adminemail;
	$AdminAddress1 = $responsearray->response->adminaddress1;
	$AdminAddress2 = $responsearray->response->adminaddress2;
	$AdminCity = $responsearray->response->admincity;
	$AdminStateProvince = $responsearray->response->adminstate;
	$AdminPostalCode = $responsearray->response->adminpostcode;
	$AdminCountry = $responsearray->response->admincountry;
	$AdminPhone = $responsearray->response->adminphone;
	$AdminFax = $responsearray->response->adminfax;


	$Techfirstname = $responsearray->response->techFirstName;
	$TechLastName = $responsearray->response->techLastname;
    $TechOrganizationName = $responsearray->response->techorg;

	$TechEmailAddress = $responsearray->response->techemail;
	$TechAddress1 = $responsearray->response->techaddress1;
	$TechAddress2 = $responsearray->response->techaddress2;
	$TechCity = $responsearray->response->techcity;
	$TechStateProvince = $responsearray->response->techstate;
	$TechPostalCode = $responsearray->response->techpostcode;
	$TechCountry = $responsearray->response->techcountry;
	$TechPhone = $responsearray->response->techphone;
	$TechFax = $responsearray->response->techfax;


	if ($codeNode != "1000") {

		$values["error"] = 'Error: ' . $codeNode . ' - ' . $message;
	}

	#####################################
    if(!isgrdomain($sld . "." . $tld) and !iseudomain($sld . "." . $tld)) {
	$values["Registrant"]['First Name'] = $firstname;
	$values["Registrant"]['Last Name'] = $LastName;
    }
    if(iseudomain($sld . "." . $tld)){
        $values["Registrant"]['Full Name'] = $fullname;
    }
	$values["Registrant"]['Organisation Name'] = $OrganizationName;
    if(isgrdomain($sld . "." . $tld)){
        $values["Registrant"]['Company Title'] = $CompanyTitle;
    }
	$values["Registrant"]['Email'] = $EmailAddress;
	$values["Registrant"]['Address 1'] = $Address1;
	$values["Registrant"]['Address 2'] = $Address2;
	$values["Registrant"]['City'] = $City;
	$values["Registrant"]['State'] = $StateProvince;
	$values["Registrant"]['Postcode'] = $PostalCode;
	$values["Registrant"]['Country'] = $Country;
	$values["Registrant"]['Phone'] = $Phone;
	$values["Registrant"]['Fax'] = $Fax;

    if(iseudomain($sld . "." . $tld)){
    //    $values["Registrant"]['naturalPerson'] = $naturalPerson;
        $values["Registrant"]['citizenship'] = $citizenship;

    }

    if(!iseudomain($sld . "." . $tld)) {
	$values["Admin"]['First Name'] = $Adminfirstname;
	$values["Admin"]['Last Name'] = $AdminLastName;
	$values["Admin"]['Organisation Name'] = $AdminOrganizationName;
	$values["Admin"]['Email'] = $AdminEmailAddress;
	$values["Admin"]['Address 1'] = $AdminAddress1;
	$values["Admin"]['Address 2'] = $AdminAddress2;
	$values["Admin"]['City'] = $AdminCity;
	$values["Admin"]['State'] = $AdminStateProvince;
	$values["Admin"]['Postcode'] = $AdminPostalCode;
	$values["Admin"]['Country'] = $AdminCountry;
	$values["Admin"]['Phone'] = $AdminPhone;
	$values["Admin"]['Fax'] = $AdminFax;

	$values["Tech"]['First Name'] = $Techfirstname;
	$values["Tech"]['Last Name'] = $TechLastName;
	$values["Tech"]['Organisation Name'] = $TechOrganizationName;
	$values["Tech"]['Email'] = $TechEmailAddress;
	$values["Tech"]['Address 1'] = $TechAddress1;
	$values["Tech"]['Address 2'] = $TechAddress2;
	$values["Tech"]['City'] = $TechCity;
	$values["Tech"]['State'] = $TechStateProvince;
	$values["Tech"]['Postcode'] = $TechPostalCode;
	$values["Tech"]['Country'] = $TechCountry;
	$values["Tech"]['Phone'] = $TechPhone;
	$values["Tech"]['Fax'] = $TechFax;
    }

	return $values;
}

function papaki_SaveContactDetails($params)
{
	$values = array();
	$username = '';
	$password = '';
	$apikey = encodetolatin($params["APIkey"]);
	$tld = encodetolatin($params["tld"]);
	$sld = encodetolatin($params["sld"]);


    $firstname = encodetolatin($params['contactdetails']["Registrant"]['First Name']);
    $lastname = encodetolatin($params['contactdetails']["Registrant"]['Last Name']);
        $fullname = $firstname . " " . $lastname;
    if (iseudomain($sld . "." . $tld)) {
        $fullname = encodetolatin($params['contactdetails']["Registrant"]['Full Name']);
    }
    $companyName = encodetolatin($params['contactdetails']["Registrant"]['Organisation Name']);
    $EmailAddress = encodetolatin($params['contactdetails']["Registrant"]['Email']);
    $Address1 = encodetolatin($params['contactdetails']["Registrant"]['Address 1']);
    $Address2 = encodetolatin($params['contactdetails']["Registrant"]['Address 2']);
    $City = encodetolatin($params['contactdetails']["Registrant"]['City']);
    $StateProvince = encodetolatin($params['contactdetails']["Registrant"]['State']);
    $PostalCode = encodetolatin($params['contactdetails']["Registrant"]['Postcode']);
    $Country = encodetolatin($params['contactdetails']["Registrant"]['Country']);
    $Phone = encodetolatin($params["contactdetails"]["Registrant"]['Phone']);

    $naturalPerson="True";
    if(trim($companyName)!=""){
        $naturalPerson="False";
    }
    $citizenship = encodetolatin($params["contactdetails"]["Registrant"]["citizenship"]);


    if ($params["contactdetails"]["Registrant"]['Phone Country Code'] and !(startswith($params['contactdetails']["Registrant"]['Phone'],
            "+")) and !(startswith($params['contactdetails']["Registrant"]['Phone'], "00"))) {
        $Phone = '+' . $params["contactdetails"]["Registrant"]['Phone Country Code'] . "." . $Phone;
    }
    $Fax = encodetolatin($params['contactdetails']["Registrant"]['Fax']);
    if ($Fax == "+30.2" or $Fax == "+30.") {
		$Fax = "";
	}
    $adminfirstname = encodetolatin($params['contactdetails']["Admin"]['First Name']);
    $adminlastname = encodetolatin($params['contactdetails']["Admin"]['Last Name']);
		$adminfullname = $adminfirstname . " " . $adminlastname;
    $admincompanyName = encodetolatin($params['contactdetails']["Admin"]['Organisation Name']);

    if (isgrdomain($sld . "." . $tld)) {
        if (trim($admincompanyName) == "") {
            $admincompanyName=$adminfullname;
        }
    }

    $AdminEmailAddress = encodetolatin($params['contactdetails']["Admin"]['Email']);
    $AdminAddress1 = encodetolatin($params['contactdetails']["Admin"]['Address 1']);
    $AdminAddress2 = encodetolatin($params['contactdetails']["Admin"]['Address 2']);
    $AdminCity = encodetolatin($params['contactdetails']["Admin"]['City']);
    $AdminStateProvince = encodetolatin($params['contactdetails']["Admin"]['State']);
    $AdminPostalCode = encodetolatin($params['contactdetails']["Admin"]['Postcode']);
    $AdminCountry = encodetolatin($params['contactdetails']["Admin"]['Country']);
    $AdminPhone = encodetolatin($params['contactdetails']["Admin"]['Phone']);
    if ($params["contactdetails"]["Admin"]['Phone Country Code'] and !(startswith($params['contactdetails']["Admin"]['Phone'],
            "+")) and !(startswith($params['contactdetails']["Admin"]['Phone'], "00"))) {
        $AdminPhone = '+' . $params["contactdetails"]["Admin"]['Phone Country Code'] . "." . $AdminPhone;
    }
    $AdminFax = encodetolatin($params['contactdetails']["Admin"]['Fax']);
    if ($AdminFax == "+30.2" or $AdminFax == "+30.") {
		$AdminFax = "";
	}

    $techfirstname = encodetolatin($params['contactdetails']["Tech"]['First Name']);
    $techlastname = encodetolatin($params['contactdetails']["Tech"]['Last Name']);
		$techfullname = $techfirstname . " " . $techlastname;
    $techcompanyName = encodetolatin($params['contactdetails']["Tech"]['Organisation Name']);
    if (isgrdomain($sld . "." . $tld)) {
        if (trim($techcompanyName) == "") {
            $techcompanyName=$techfullname;
        }
    }
    $TechEmailAddress = encodetolatin($params['contactdetails']["Tech"]['Email']);
    $TechAddress1 = encodetolatin($params['contactdetails']["Tech"]['Address 1']);
    $TechAddress2 = encodetolatin($params['contactdetails']["Tech"]['Address 2']);
    $TechCity = encodetolatin($params['contactdetails']["Tech"]['City']);
    $TechStateProvince = encodetolatin($params['contactdetails']["Tech"]['State']);
    $TechPostalCode = encodetolatin($params['contactdetails']["Tech"]['Postcode']);
    $TechCountry = encodetolatin($params['contactdetails']["Tech"]['Country']);
    $TechPhone = encodetolatin($params['contactdetails']["Tech"]['Phone']);
    if ($params["contactdetails"]["Tech"]['Phone Country Code'] and !(startswith($params['contactdetails']["Tech"]['Phone'],
            "+")) and !(startswith($params['contactdetails']["Tech"]['Phone'], "00"))) {

        $TechPhone = '+' . $params["contactdetails"]["Tech"]['Phone Country Code'] . "." . $TechPhone;
    }
    $TechFax = encodetolatin($params['contactdetails']["Tech"]['Fax']);
    if ($TechFax == "+30.2" or $TechFax == "+30.") {
		$TechFax = "";
	}

    //fix empty chars
    $Phone=strtr($Phone,array(" " => ""));
    $AdminPhone=strtr($AdminPhone,array(" " => ""));
    $TechPhone=strtr($TechPhone,array(" " => ""));

    if($AdminEmailAddress=='' and $adminfirstname==''  and $adminlastname==''){
        $AdminCountry='';
        $AdminPhone='';
        $AdminFax='';
    }

    if($TechEmailAddress=='' and $techfirstname==''  and $techlastname==''){
        $TechCountry='';
        $TechPhone='';
        $TechFax='';
    }

	if (trim($EmailAddress) == '') {
		$EmailAddress = ' ';
	}
	if (trim($Address1) == '') {
		$Address1 = ' ';
	}
	if (trim($Address2) == '') {
		$Address2 = ' ';
	}
	if (trim($City) == '') {
		$City = ' ';
	}
	if (trim($StateProvince) == '') {
		$StateProvince = ' ';
	}
	if (trim($PostalCode) == '') {
		$PostalCode = ' ';
	}
	if (trim($Country) == '') {
		$Country = ' ';
	}
	if (trim($Phone) == '') {
		$Phone = ' ';
	}
	if (trim($Fax) == '') {
		$Fax = ' ';
	}
	if (trim($AdminEmailAddress) == '') {
		$AdminEmailAddress = ' ';
	}
	if (trim($AdminAddress1) == '') {
		$AdminAddress1 = ' ';
	}
	if (trim($AdminAddress2) == '') {
		$AdminAddress2 = ' ';
	}
	if (trim($AdminCity) == '') {
		$AdminCity = ' ';
	}
	if (trim($AdminStateProvince) == '') {
		$AdminStateProvince = ' ';
	}
	if (trim($AdminPostalCode) == '') {
		$AdminPostalCode = ' ';
	}
	if (trim($AdminCountry) == '') {
		$AdminCountry = ' ';
	}
	if (trim($AdminPhone) == '') {
		$AdminPhone = ' ';
	}
	if (trim($AdminFax) == '') {
		$AdminFax = ' ';
	}
	if (trim($TechEmailAddress) == '') {
		$TechEmailAddress = ' ';
	}
	if (trim($TechAddress1) == '') {
		$TechAddress1 = ' ';
	}
	if (trim($TechAddress2) == '') {
		$TechAddress2 = ' ';
	}
	if (trim($TechCity) == '') {
		$TechCity = ' ';
	}
	if (trim($TechStateProvince) == '') {
		$TechStateProvince = ' ';
	}
	if (trim($TechPostalCode) == '') {
		$TechPostalCode = ' ';
	}
	if (trim($TechCountry) == '') {
		$TechCountry = ' ';
	}
	if (trim($TechPhone) == '') {
		$TechPhone = ' ';
	}
	if (trim($TechFax) == '') {
		$TechFax = ' ';
	}
	if (trim($firstname) == '') {
		$firstname = ' ';
	}
	if (trim($adminfirstname) == '') {
		$adminfirstname = ' ';
	}
	if (trim($techfirstname) == '') {
		$techfirstname = ' ';
	}
	if (trim($lastname) == '') {
		$lastname = ' ';
	}
	if (trim($adminlastname) == '') {
		$adminlastname = ' ';
	}
	if (trim($techlastname) == '') {
		$techlastname = ' ';
	}
	if (trim($fullname) == '') {
		$fullname = ' ';
	}
	if (trim($adminfullname) == '') {
		$adminfullname = ' ';
	}
	if (trim($techfullname) == '') {
		$techfullname = ' ';
	}


	########################


    if (isgrdomain($sld . "." . $tld)  ) {
		$fullname = ' ';
		$firstname = ' ';
		$lastname = ' ';

	}

	$json = new Services_JSON();
	$jsonarray = array(
		"request" => array(
			"do" => 'savecontactdetails',
			"username" => $username,
			"password" => $password,
			"apiKey" => $apikey,
			"domainname" => $sld . "." . $tld,
			"firstname" => $firstname,
			"lastname" => $lastname,
			"fullname" => $fullname,
            "CompanyName" => $companyName,
			"emailaddress" => $EmailAddress,
			"address1" => $Address1,
			"address2" => $Address2,
			"city" => $City,
			"stateprovince" => $StateProvince,
			"postalcode" => $PostalCode,
			"country" => $Country,
			"phone" => $Phone,
			"fax" => $Fax,
			"adminfirstname" => $adminfirstname,
			"adminlastname" => $adminlastname,
			"adminfullname" => $adminfullname,
            "adminCompanyName" => $admincompanyName,
			"adminemailaddress" => $AdminEmailAddress,
			"adminaddress1" => $AdminAddress1,
			"adminaddress2" => $AdminAddress2,
			"admincity" => $AdminCity,
			"adminstateprovince" => $AdminStateProvince,
			"adminpostalcode" => $AdminPostalCode,
			"admincountry" => $AdminCountry,
			"adminphone" => $AdminPhone,
			"adminfax" => $AdminFax,
			"techfirstname" => $techfirstname,
			"techlastname" => $techlastname,
			"techfullname" => $techfullname,
            "techCompanyName" => $techcompanyName,
			"techemailaddress" => $TechEmailAddress,
			"techaddress1" => $TechAddress1,
			"techaddress2" => $TechAddress2,
			"techcity" => $TechCity,
			"techstateprovince" => $TechStateProvince,
			"techpostalcode" => $TechPostalCode,
			"techcountry" => $TechCountry,
			"techphone" => $TechPhone,
			"techfax" => $TechFax
		)
	);

    if (iseudomain( $sld . "." . $tld) ){
        $jsonarray["request"]["naturalPerson"] =$naturalPerson;
        $jsonarray["request"]["citizenship"] =$citizenship;

    }

	$Xpost = $json->encode($jsonarray);
	$Xpost = latintogreek($Xpost);
	$Xpost = str_replace("\n", "", $Xpost);
	$Xpost = str_replace("&", urlencode("&"), $Xpost);


	$pageContents = HttpClient::quickPost($params["PostUrl"], array(
		'message' => $Xpost
	));
	$responsearray = $json->decode($pageContents);
	$codeNode = $responsearray->response->code;
	//warnings
	if ($codeNode == "190" or $codeNode == "180") {
		$codeNode = "1000";
	}
	$message = $responsearray->response->message;


	if ($codeNode != "1000") {

		$values["error"] = 'Error: ' . $codeNode . ' - ' . $message;
	}


	return $values;
}

function papaki_GetEPPCode($params)
{

	$values = array();

	$apikey = encodetolatin($params["APIkey"]);
	$tld = encodetolatin($params["tld"]);
	$sld = encodetolatin($params["sld"]);


	$json = new Services_JSON();
	$jsonarray = array(
		"request" => array(
			"do" => 'getauthocode',
			"apiKey" => $apikey,
			"domainname" => $sld . "." . $tld
		)
	);
	$Xpost = $json->encode($jsonarray);
	$Xpost = latintogreek($Xpost);


	$pageContents = HttpClient::quickPost($params["PostUrl"], array(
		'message' => $Xpost
	));


	$responsearray = $json->decode($pageContents);
	$codeNode = $responsearray->response->code;
	$message = $responsearray->response->message;


	$eppcode = $responsearray->response->eppcode;


	if ($codeNode != "1000") {

		$values["error"] = 'Error: ' . $codeNode . ' - ' . $message;
	}


	$values["eppcode"] = $eppcode;

	return $values;
}

function papaki_RegisterNameserver($params)
{
	$values = array();
	$apikey = encodetolatin($params["APIkey"]);
	$tld = encodetolatin($params["tld"]);
	$sld = encodetolatin($params["sld"]);
	$nameserver = encodetolatin($params["nameserver"]);
	$ipaddress = encodetolatin($params["ipaddress"]);


	$json = new Services_JSON();
	$jsonarray = array(
		"request" => array(
			"do" => 'registerns',
			"apiKey" => $apikey,
			"domainname" => $sld . "." . $tld,
			"ns" => $nameserver,
			"ip" => $ipaddress
		)
	);
	$Xpost = $json->encode($jsonarray);
	$Xpost = latintogreek($Xpost);


	$pageContents = HttpClient::quickPost($params["PostUrl"], array(
		'message' => $Xpost
	));


	$responsearray = $json->decode($pageContents);
	$codeNode = $responsearray->response->code;
	$message = $responsearray->response->message;


	if ($codeNode != "1000") {

		$values["error"] = 'Error: ' . $codeNode . ' - ' . $message;
	}


	return $values;
}

function papaki_ModifyNameserver($params)
{
	$values = array();
	$apikey = encodetolatin($params["APIkey"]);
	$tld = encodetolatin($params["tld"]);
	$sld = encodetolatin($params["sld"]);
	$nameserver = encodetolatin($params["nameserver"]);
	$currentipaddress = encodetolatin($params["currentipaddress"]);
	$newipaddress = encodetolatin($params["newipaddress"]);


	$json = new Services_JSON();
	$jsonarray = array(
		"request" => array(
			"do" => 'modifyns',
			"apiKey" => $apikey,
			"domainname" => $sld . "." . $tld,
			"ns" => $nameserver,
			"oldip" => $currentipaddress,
			"newip" => $newipaddress
		)
	);
	$Xpost = $json->encode($jsonarray);
	$Xpost = latintogreek($Xpost);


	$pageContents = HttpClient::quickPost($params["PostUrl"], array(
		'message' => $Xpost
	));

	$responsearray = $json->decode($pageContents);
	$codeNode = $responsearray->response->code;
	$message = $responsearray->response->message;


	if ($codeNode != "1000") {

		$values["error"] = 'Error: ' . $codeNode . ' - ' . $message;
	}


	return $values;
}

function papaki_DeleteNameserver($params)
{
	$values = array();
//    $username = '';
//    $password = '';
//    $apikey = encodetolatin($params["APIkey"]);
//    $tld = encodetolatin($params["tld"]);
//    $sld = encodetolatin($params["sld"]);
//    $nameserver = encodetolatin($params["nameserver"]);
//
//
//    $json = new Services_JSON();
//    $jsonarray = array(
//        "request" => array(
//            "do" => 'deletens',
//            "username" => $username,
//            "password" => $password,
//            "apiKey" => $apikey,
//            "domainname" => $sld . "." . $tld,
//            "ns" => $nameserver
//        )
//    );
//    $Xpost = $json->encode($jsonarray);
//    $Xpost = latintogreek($Xpost);
//
//
//    $pageContents = HttpClient::quickPost($params["PostUrl"], array(
//        'message' => $Xpost
//    ));
//    $responsearray = $json->decode($pageContents);
//    $codeNode = $responsearray->response->code;
//    $message = $responsearray->response->message;


	$values["error"] = 'Error: Not Supported';

	return $values;
}


function papaki_Sync($params)
{
	$values = array();

	//3 seconds delay 3000000
	usleep(3000000);


# Other parameters used in your _getConfigArray() function would also be available for use in this function


# Put your code to check on the domain status here

	$apikey = encodetolatin($params["APIkey"]);
	$domain = encodetolatin($params["domain"]);

# Other parameters used in your _getConfigArray() function would also be available for use in this function

# Put your code to check on the domain transfer status here


	$json = new Services_JSON();
	$jsonarray = array("request" => array("do" => 'getDomainInfo', "apiKey" => $apikey, "domainname" => $domain));
	$Xpost = $json->encode($jsonarray);
	$Xpost = latintogreek($Xpost);


	$pageContents = HttpClient::quickPost($params["PostUrl"], array(
		'message' => $Xpost
	));
	$responsearray = $json->decode($pageContents);
	$codeNode = $responsearray->response->code;
	$message = $responsearray->response->message;


	if ($codeNode == "1000") {
		$expirydate = $responsearray->response->expirationDate;


		if ($expirydate != "") {
			$values['expirydate'] = $expirydate;
		}
	} else {

		$values['error'] = "Error at  Response from Papaki<br>Domain: " . $domain . "<br>Code: " . $codeNode . "<br>Message: " . $message . "<br><br>"; # error if the check fails - for example domain not found

	}


	return $values; # return the details of the sync

}


function papaki_TransferSync($params)
{
	$values = array();
	// wait for 2 seconds
	usleep(3000000);


	$apikey = encodetolatin($params["APIkey"]);
	$domain = encodetolatin($params["domain"]);

# Other parameters used in your _getConfigArray() function would also be available for use in this function

# Put your code to check on the domain transfer status here


	$json = new Services_JSON();
	$jsonarray = array("request" => array("do" => 'getDomainInfo', "apiKey" => $apikey, "domainname" => $domain));
	$Xpost = $json->encode($jsonarray);
	$Xpost = latintogreek($Xpost);

	//  $headers = array('Content-type: application/x-www-form-urlencoded');

	$pageContents = HttpClient::quickPost($params["PostUrl"], array(
		'message' => $Xpost
	));
	$responsearray = $json->decode($pageContents);
	$codeNode = $responsearray->response->code;
	$message = $responsearray->response->message;


	# - if the transfer has completed successfully


	if ($codeNode == "1000") {
		$expirydate = $responsearray->response->expirationDate;


		if ($expirydate != "") {
			$values['completed'] = true; #  when transfer completes successfully

			$values['expirydate'] = $expirydate;
		}
	}


	# - or if failed


	# - or if errored
	if ($codeNode != "1000") {
		$values['error'] = "Error at  Response from Papaki<br>Domain: " . $domain . "<br>Code: " . $codeNode . "<br>Message: " . $message . "<br><br>"; # error if the check fails - for example domain not found
	}


	return $values; # return the details of the sync

}




function startsWith($haystack, $needle)
{
	return $needle === "" || strpos($haystack, $needle) === 0;
}

function isgrdomain($domainname)
{

	return substr($domainname, strlen('.gr') * -1) == '.gr';
}


function iseudomain($domainname)
{

	return substr($domainname, strlen('.eu') * -1) == '.eu';
}

function encodetolatin($mystring)
{


	$mystring = str_replace("Α", "&Alpha;", $mystring);
	$mystring = str_replace("Β", "&Beta;", $mystring);
	$mystring = str_replace("Γ", "&Gamma;", $mystring);
	$mystring = str_replace("Δ", "&Delta;", $mystring);
	$mystring = str_replace("Ε", "&Epsilon;", $mystring);
	$mystring = str_replace("Ζ", "&Zeta;", $mystring);
	$mystring = str_replace("Η", "&Eta;", $mystring);
	$mystring = str_replace("Θ", "&Theta;", $mystring);
	$mystring = str_replace("Ι", "&Iota;", $mystring);
	$mystring = str_replace("Κ", "&Kappa;", $mystring);
	$mystring = str_replace("Λ", "&Lambda;", $mystring);
	$mystring = str_replace("Μ", "&Mu;", $mystring);
	$mystring = str_replace("Ν", "&Nu;", $mystring);
	$mystring = str_replace("Ξ", "&Xi;", $mystring);
	$mystring = str_replace("Ο", "&Omicron;", $mystring);
	$mystring = str_replace("Π", "&Pi;", $mystring);
	$mystring = str_replace("Ρ", "&Rho;", $mystring);
	$mystring = str_replace("Σ", "&Sigma;", $mystring);
	$mystring = str_replace("Τ", "&Tau;", $mystring);
	$mystring = str_replace("Υ", "&Upsilon;", $mystring);
	$mystring = str_replace("Φ", "&Phi;", $mystring);
	$mystring = str_replace("Χ", "&Chi;", $mystring);
	$mystring = str_replace("Ψ", "&Psi;", $mystring);
	$mystring = str_replace("Ω", "&Omega;", $mystring);
	$mystring = str_replace("α", "&alpha;", $mystring);
	$mystring = str_replace("β", "&beta;", $mystring);
	$mystring = str_replace("γ", "&gamma;", $mystring);
	$mystring = str_replace("δ", "&delta;", $mystring);
	$mystring = str_replace("ε", "&epsilon;", $mystring);
	$mystring = str_replace("ζ", "&zeta;", $mystring);
	$mystring = str_replace("η", "&eta;", $mystring);
	$mystring = str_replace("θ", "&theta;", $mystring);
	$mystring = str_replace("ι", "&iota;", $mystring);
	$mystring = str_replace("κ", "&kappa;", $mystring);
	$mystring = str_replace("λ", "&lambda;", $mystring);
	$mystring = str_replace("μ", "&mu;", $mystring);
	$mystring = str_replace("ν", "&nu;", $mystring);
	$mystring = str_replace("ξ", "&xi;", $mystring);
	$mystring = str_replace("ο", "&omicron;", $mystring);
	$mystring = str_replace("π", "&pi;", $mystring);
	$mystring = str_replace("ρ", "&rho;", $mystring);
	$mystring = str_replace("σ", "&sigma;", $mystring);
	$mystring = str_replace("τ", "&tau;", $mystring);
	$mystring = str_replace("υ", "&upsilon;", $mystring);
	$mystring = str_replace("φ", "&phi;", $mystring);
	$mystring = str_replace("χ", "&chi;", $mystring);
	$mystring = str_replace("ψ", "&psi;", $mystring);
	$mystring = str_replace("ω", "&omega;", $mystring);
	$mystring = str_replace("ς", "&sigmaf;", $mystring);


	$mystring = str_replace("ά", "&#940;", $mystring);
	$mystring = str_replace("έ", "&#941;", $mystring);
	$mystring = str_replace("ώ", "&#974;", $mystring);
	$mystring = str_replace("ύ", "&#973;", $mystring);
	$mystring = str_replace("ί", "&#943;", $mystring);
	$mystring = str_replace("ό", "&#972;", $mystring);
	$mystring = str_replace("ή", "&#942;", $mystring);
	$mystring = str_replace("Ά", "&#902;", $mystring);
	$mystring = str_replace("Έ", "&#904;", $mystring);
	$mystring = str_replace("Ώ", "&#911;", $mystring);
	$mystring = str_replace("Ύ", "&#910;", $mystring);
	$mystring = str_replace("Ί", "&#906;", $mystring);
	$mystring = str_replace("Ό", "&#908;", $mystring);
	$mystring = str_replace("Ή", "&#905;", $mystring);

	$mystring = str_replace("ϊ", "&#970;", $mystring);
	$mystring = str_replace("ΐ", "&#912;", $mystring);
	$mystring = str_replace("ϋ", "&#971;", $mystring);
	$mystring = str_replace("ΰ", "&#944;", $mystring);

	return $mystring;
}


function latintogreek($mystring)
{


	$mystring = str_replace("&Alpha;", "Α", $mystring);
	$mystring = str_replace("&Beta;", "Β", $mystring);
	$mystring = str_replace("&Gamma;", "Γ", $mystring);
	$mystring = str_replace("&Delta;", "Δ", $mystring);
	$mystring = str_replace("&Epsilon;", "Ε", $mystring);
	$mystring = str_replace("&Zeta;", "Ζ", $mystring);
	$mystring = str_replace("&Eta;", "Η", $mystring);
	$mystring = str_replace("&Theta;", "Θ", $mystring);
	$mystring = str_replace("&Iota;", "Ι", $mystring);
	$mystring = str_replace("&Kappa;", "Κ", $mystring);
	$mystring = str_replace("&Lambda;", "Λ", $mystring);
	$mystring = str_replace("&Mu;", "Μ", $mystring);
	$mystring = str_replace("&Nu;", "Ν", $mystring);
	$mystring = str_replace("&Xi;", "Ξ", $mystring);
	$mystring = str_replace("&Omicron;", "Ο", $mystring);
	$mystring = str_replace("&Pi;", "Π", $mystring);
	$mystring = str_replace("&Rho;", "Ρ", $mystring);
	$mystring = str_replace("&Sigma;", "Σ", $mystring);
	$mystring = str_replace("&Tau;", "Τ", $mystring);
	$mystring = str_replace("&Upsilon;", "Υ", $mystring);
	$mystring = str_replace("&Phi;", "Φ", $mystring);
	$mystring = str_replace("&Chi;", "Χ", $mystring);
	$mystring = str_replace("&Psi;", "Ψ", $mystring);
	$mystring = str_replace("&Omega;", "Ω", $mystring);
	$mystring = str_replace("&alpha;", "α", $mystring);
	$mystring = str_replace("&beta;", "β", $mystring);
	$mystring = str_replace("&gamma;", "γ", $mystring);
	$mystring = str_replace("&delta;", "δ", $mystring);
	$mystring = str_replace("&epsilon;", "ε", $mystring);
	$mystring = str_replace("&zeta;", "ζ", $mystring);
	$mystring = str_replace("&eta;", "η", $mystring);
	$mystring = str_replace("&theta;", "θ", $mystring);
	$mystring = str_replace("&iota;", "ι", $mystring);
	$mystring = str_replace("&kappa;", "κ", $mystring);
	$mystring = str_replace("&lambda;", "λ", $mystring);
	$mystring = str_replace("&mu;", "μ", $mystring);
	$mystring = str_replace("&nu;", "ν", $mystring);
	$mystring = str_replace("&xi;", "ξ", $mystring);
	$mystring = str_replace("&omicron;", "ο", $mystring);
	$mystring = str_replace("&pi;", "π", $mystring);
	$mystring = str_replace("&rho;", "ρ", $mystring);
	$mystring = str_replace("&sigma;", "σ", $mystring);
	$mystring = str_replace("&tau;", "τ", $mystring);
	$mystring = str_replace("&upsilon;", "υ", $mystring);
	$mystring = str_replace("&phi;", "φ", $mystring);
	$mystring = str_replace("&chi;", "χ", $mystring);
	$mystring = str_replace("&psi;", "ψ", $mystring);
	$mystring = str_replace("&omega;", "ω", $mystring);
	$mystring = str_replace("&sigmaf;", "ς", $mystring);


	$mystring = str_replace("&#940;", "ά", $mystring);
	$mystring = str_replace("&#941;", "έ", $mystring);
	$mystring = str_replace("&#974;", "ώ", $mystring);
	$mystring = str_replace("&#973;", "ύ", $mystring);
	$mystring = str_replace("&#943;", "ί", $mystring);
	$mystring = str_replace("&#972;", "ό", $mystring);
	$mystring = str_replace("&#942;", "ή", $mystring);
	$mystring = str_replace("&#902;", "Ά", $mystring);
	$mystring = str_replace("&#904;", "Έ", $mystring);
	$mystring = str_replace("&#911;", "Ώ", $mystring);
	$mystring = str_replace("&#910;", "Ύ", $mystring);
	$mystring = str_replace("&#906;", "Ί", $mystring);
	$mystring = str_replace("&#908;", "Ό", $mystring);
	$mystring = str_replace("&#905;", "Ή", $mystring);

	$mystring = str_replace("&#970;", "ϊ", $mystring);
	$mystring = str_replace("&#912;", "ΐ", $mystring);
	$mystring = str_replace("&#971;", "ϋ", $mystring);
	$mystring = str_replace("&#944;", "ΰ", $mystring);

	return $mystring;
}

?>