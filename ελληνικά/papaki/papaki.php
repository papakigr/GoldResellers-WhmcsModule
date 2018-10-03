<?php

use Illuminate\Database\Capsule\Manager as Capsule;

set_time_limit(200);

require('libs/ActiveLink/alink_include.php');
import('org.active-link.xml.XMLDocument');

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
            "FriendlyName" => "Περιορισμός πολλαπλών ανανεώσεων",
            "Type" => "yesno",
            "Description" => "Εάν είναι τσεκαρισμένο περιορίζει τις πολλαπλές ανανεώσεις ονομάτων χώρου σε διάστημα 24 ωρών. Προτείνουμε για λόγους ασφαλείας να το έχετε τσεκαρισμένο σε περίπτωση που σταλεί κάποια ανανέωση πολλές φορές χωρίς να το επιθυμείτε",
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
//////////////////////////////////////////////////////////////////////////////////////////////////


    //Find contactDetails
    $results = Capsule::table('tbldomains')
        ->join('tblorders', 'tbldomains.orderid', '=', 'tblorders.id')
        ->where('tbldomains.id', '=', $params["domainid"])
        ->select('tblorders.contactid', 'tbldomains.userid')
        ->get();

    foreach ($results as $row) {


        if ($row->contactid > 0) {
            $contactarray = Capsule::table('tblcontacts')->where('id', '=', $row->contactid)->select('companyname',
                'firstname', 'lastname', 'address1', 'address2', 'city', 'state', 'postcode', 'country', 'email',
                'phonenumber')->get();
        } else {
            $contactarray = Capsule::table('tblclients')->where('id', '=', $row->userid)->select('companyname',
                'firstname', 'lastname', 'address1', 'address2', 'city', 'state', 'postcode', 'country', 'email',
                'phonenumber')->get();


        }

        $params["companyname"] = $contactarray['0']->companyname;
        $params["firstname"] = $contactarray['0']->firstname;
        $params["lastname"] = $contactarray['0']->lastname;
        $params["address1"] = $contactarray['0']->address1;
        $params["address2"] = $contactarray['0']->address2;
        $params["city"] = $contactarray['0']->city;
        $params["state"] = $contactarray['0']->state;
        $params["postcode"] = $contactarray['0']->postcode;
        $params["country"] = $contactarray['0']->country;
        $params["email"] = $contactarray['0']->email;
        $params["phonenumber"] = $contactarray['0']->phonenumber;
    }


/////////////////////////////////////////////////////////////


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
    $extra_country = encodetolatin($params["country"]);

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
    //end extra attributes

    $tld = encodetolatin($params["tld"]);
    $sld = encodetolatin($params["sld"]);

    $idprotection = encodetolatin($params["idprotection"]);
    if ($idprotection == "1") {
        $idprotection = "true";
    } else {
        $idprotection = "false";
    }

    $params["phonenumber"]=strtr($params["phonenumber"],array(" " => ""));

    if (!(startswith($params["phonenumber"], "+")) and !(startswith($params["phonenumber"], "00"))) {
        $params["phonenumber"] = '+30.' . $params["phonenumber"];

    }

    $json = new Services_JSON();
    $jsonarray = array(
        "request" => array(
            "do" => 'domainregister',
            "username" => $username,
            "password" => $password,
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
            "owner_fullname" => encodetolatin($params["companyname"]),
            "owner_firstname" => encodetolatin($params["firstname"]),
            "owner_lastname" => encodetolatin($params["lastname"]),
            "owner_email" => encodetolatin($params["email"]),
            "owner_address" => encodetolatin($params["address1"]),
            "owner_state" => encodetolatin($params["state"]),
            "owner_city" => encodetolatin($params["city"]),
            "owner_postcode" => encodetolatin($params["postcode"]),
            "owner_country" => encodetolatin($params["country"]),
            "owner_phone" => encodetolatin($params["phonenumber"]),
            "owner_fax" => '+30.2',
            "owner_litepsd" => ' ',
            "owner_title" => ' ',
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


    //Find contactDetails
    $results = Capsule::table('tbldomains')
        ->join('tblorders', 'tbldomains.orderid', '=', 'tblorders.id')
        ->where('tbldomains.id', '=', $params["domainid"])
        ->select('tblorders.contactid', 'tbldomains.userid')
        ->get();

    foreach ($results as $row) {


        if ($row->contactid > 0) {
            $contactarray = Capsule::table('tblcontacts')->where('id', '=', $row->contactid)->select('companyname',
                'firstname', 'lastname', 'address1', 'address2', 'city', 'state', 'postcode', 'country', 'email',
                'phonenumber')->get();
        } else {
            $contactarray = Capsule::table('tblclients')->where('id', '=', $row->userid)->select('companyname',
                'firstname', 'lastname', 'address1', 'address2', 'city', 'state', 'postcode', 'country', 'email',
                'phonenumber')->get();


        }
        $params["companyname"] = $contactarray{'companyname'};
        $params["firstname"] = $contactarray{'firstname'};
        $params["lastname"] = $contactarray{'lastname'};
        $params["address1"] = $contactarray{'address1'};
        $params["address2"] = $contactarray{'address2'};
        $params["city"] = $contactarray{'city'};
        $params["state"] = $contactarray{'state'};
        $params["postcode"] = $contactarray{'postcode'};
        $params["country"] = $contactarray{'country'};
        $params["email"] = $contactarray{'email'};
        $params["phonenumber"] = $contactarray{'phonenumber'};
    }


/////////////////////////////////////////////////////////////


    $username = '';
    $password = '';
    $apikey = encodetolatin($params["APIkey"]);
    $tld = encodetolatin($params["tld"]);
    $sld = encodetolatin($params["sld"]);
    $transfersecret = encodetolatin($params["transfersecret"]);


    if (trim($transfersecret) == "") {
        $transfersecret = encodetolatin($_POST["eppcode"]);
    }
    if (trim($transfersecret) == "") {
        $transfersecret = encodetolatin($_SESSION["eppcode"]);
    }


    if (trim($transfersecret) == "") {

        for ($i = 0; $i < count($_SESSION['cart']['domains']); $i++) {
            $mydomain = $sld . "." . $tld;
            if ($_SESSION['cart']['domains'][$i]['domain'] == $mydomain) {
                $transfersecret = encodetolatin($_SESSION['cart']['domains'][$i]['eppcode']);
            }
        }


    }

    $params["phonenumber"]=strtr($params["phonenumber"],array(" " => ""));

    # Registrant Details
    $RegistrantFullName = encodetolatin($params["companyname"]);
    $RegistrantFirstName = encodetolatin($params["firstname"]);
    $RegistrantLastName = encodetolatin($params["lastname"]);
    $RegistrantAddress1 = encodetolatin($params["address1"]);
    $RegistrantCity = encodetolatin($params["city"]);
    $RegistrantStateProvince = encodetolatin($params["state"]);
    $RegistrantPostalCode = encodetolatin($params["postcode"]);
    $RegistrantCountry = encodetolatin($params["country"]);
    $RegistrantEmailAddress = encodetolatin($params["email"]);
    $RegistrantPhone = encodetolatin($params["phonenumber"]);
    if (!(startswith($RegistrantPhone, "+")) and !(startswith($RegistrantPhone, "00"))) {
        $RegistrantPhone = '+30.' . $RegistrantPhone;


    }


    $json = new Services_JSON();

    if (!isgrdomain($sld . "." . $tld) ) {
        $jsonarray = array(
            "request" => array(
                "do" => 'changeregistrar',
                "username" => $username,
                "password" => $password,
                "apiKey" => $apikey,
                "domainname" => $sld . "." . $tld,
                "customer_language" => 'gr',
                "authcode" => $transfersecret,
                "RegistrantContact" => array(
                    "firstname" => $RegistrantFirstName,
                    "lastname" => $RegistrantLastName,
                    "fullname" => $RegistrantFullName,
                    "email" => $RegistrantEmailAddress,
                    "address" => $RegistrantAddress1,
                    "state" => $RegistrantStateProvince,
                    "city" => $RegistrantCity,
                    "postcode" => $RegistrantPostalCode,
                    "country" => $RegistrantCountry,
                    "phone" => $RegistrantPhone,
                    "fax" => "",
                    "title" => ''
                )
            )
        );
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
            "username" => $username,
            "password" => $password,
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


    $firstname = $responsearray->response->registrantFirstName;
    $LastName = $responsearray->response->registrantLastname;
    $OrganizationName = $responsearray->response->registrantFullname;
    $JobTitle = $responsearray->response->registrantjob;
    $EmailAddress = $responsearray->response->registrantemail;
    $Address1 = $responsearray->response->registrantaddress1;
    $Address2 = $responsearray->response->registrantaddress2;
    $City = $responsearray->response->registrantcity;
    $StateProvince = $responsearray->response->registrantstate;
    $PostalCode = $responsearray->response->registrantpostcode;
    $Country = $responsearray->response->registrantcountry;
    $Phone = $responsearray->response->registrantphone;
    $Fax = $responsearray->response->registrantfax;

    $Adminfirstname = $responsearray->response->adminFirstName;
    $AdminLastName = $responsearray->response->adminLastname;
    $AdminOrganizationName = $responsearray->response->adminFullname;
    $AdminJobTitle = $responsearray->response->adminjob;
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
    $TechOrganizationName = $responsearray->response->techFullname;
    $TechJobTitle = $responsearray->response->techjob;
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

    $values["Registrant"]['First Name'] = $firstname;
    $values["Registrant"]['Last Name'] = $LastName;
    $values["Registrant"]['Organisation Name'] = $OrganizationName;
    $values["Registrant"]['Job Title'] = $JobTitle;
    $values["Registrant"]['Email'] = $EmailAddress;
    $values["Registrant"]['Address 1'] = $Address1;
    $values["Registrant"]['Address 2'] = $Address2;
    $values["Registrant"]['City'] = $City;
    $values["Registrant"]['State'] = $StateProvince;
    $values["Registrant"]['Postcode'] = $PostalCode;
    $values["Registrant"]['Country'] = $Country;
    $values["Registrant"]['Phone'] = $Phone;
    $values["Registrant"]['Fax'] = $Fax;


    $values["Admin"]['First Name'] = $Adminfirstname;
    $values["Admin"]['Last Name'] = $AdminLastName;
    $values["Admin"]['Organisation Name'] = $AdminOrganizationName;
    $values["Admin"]['Job Title'] = $AdminJobTitle;
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
    $values["Tech"]['Job Title'] = $TechJobTitle;
    $values["Tech"]['Email'] = $TechEmailAddress;
    $values["Tech"]['Address 1'] = $TechAddress1;
    $values["Tech"]['Address 2'] = $TechAddress2;
    $values["Tech"]['City'] = $TechCity;
    $values["Tech"]['State'] = $TechStateProvince;
    $values["Tech"]['Postcode'] = $TechPostalCode;
    $values["Tech"]['Country'] = $TechCountry;
    $values["Tech"]['Phone'] = $TechPhone;
    $values["Tech"]['Fax'] = $TechFax;


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


    $firstname = encodetolatin($_POST['contactdetails']["Registrant"]['First Name']);
    $lastname = encodetolatin($_POST['contactdetails']["Registrant"]['Last Name']);
    $fullname = encodetolatin($_POST['contactdetails']["Registrant"]['Organisation Name']);
    $EmailAddress = encodetolatin($_POST['contactdetails']["Registrant"]['Email']);
    $Address1 = encodetolatin($_POST['contactdetails']["Registrant"]['Address 1']);
    $Address2 = encodetolatin($_POST['contactdetails']["Registrant"]['Address 2']);
    $City = encodetolatin($_POST['contactdetails']["Registrant"]['City']);
    $StateProvince = encodetolatin($_POST['contactdetails']["Registrant"]['State']);
    $PostalCode = encodetolatin($_POST['contactdetails']["Registrant"]['Postcode']);
    $Country = encodetolatin($_POST['contactdetails']["Registrant"]['Country']);
    $Phone = encodetolatin($_POST['contactdetails']["Registrant"]['Phone']);
    $Fax = encodetolatin($_POST['contactdetails']["Registrant"]['Fax']);
    if ($Fax == "+30.2") {
        $Fax = "";
    }
    $adminfirstname = encodetolatin($_POST['contactdetails']["Admin"]['First Name']);
    $adminlastname = encodetolatin($_POST['contactdetails']["Admin"]['Last Name']);
    $adminfullname = encodetolatin($_POST['contactdetails']["Admin"]['Organisation Name']);
    if ($adminfullname == "") {
        $adminfullname = $adminfirstname . " " . $adminlastname;
    }
    $AdminEmailAddress = encodetolatin($_POST['contactdetails']["Admin"]['Email']);
    $AdminAddress1 = encodetolatin($_POST['contactdetails']["Admin"]['Address 1']);
    $AdminAddress2 = encodetolatin($_POST['contactdetails']["Admin"]['Address 2']);
    $AdminCity = encodetolatin($_POST['contactdetails']["Admin"]['City']);
    $AdminStateProvince = encodetolatin($_POST['contactdetails']["Admin"]['State']);
    $AdminPostalCode = encodetolatin($_POST['contactdetails']["Admin"]['Postcode']);
    $AdminCountry = encodetolatin($_POST['contactdetails']["Admin"]['Country']);
    $AdminPhone = encodetolatin($_POST['contactdetails']["Admin"]['Phone']);
    $AdminFax = encodetolatin($_POST['contactdetails']["Admin"]['Fax']);
    if ($AdminFax == "+30.2") {
        $AdminFax = "";
    }

    $techfirstname = encodetolatin($_POST['contactdetails']["Tech"]['First Name']);
    $techlastname = encodetolatin($_POST['contactdetails']["Tech"]['Last Name']);
    $techfullname = encodetolatin($_POST['contactdetails']["Tech"]['Organisation Name']);
    if ($techfullname == "") {
        $techfullname = $techfirstname . " " . $techlastname;
    }
    $TechEmailAddress = encodetolatin($_POST['contactdetails']["Tech"]['Email']);
    $TechAddress1 = encodetolatin($_POST['contactdetails']["Tech"]['Address 1']);
    $TechAddress2 = encodetolatin($_POST['contactdetails']["Tech"]['Address 2']);
    $TechCity = encodetolatin($_POST['contactdetails']["Tech"]['City']);
    $TechStateProvince = encodetolatin($_POST['contactdetails']["Tech"]['State']);
    $TechPostalCode = encodetolatin($_POST['contactdetails']["Tech"]['Postcode']);
    $TechCountry = encodetolatin($_POST['contactdetails']["Tech"]['Country']);
    $TechPhone = encodetolatin($_POST['contactdetails']["Tech"]['Phone']);
    $TechFax = encodetolatin($_POST['contactdetails']["Tech"]['Fax']);
    if ($TechFax == "+30.2") {
        $TechFax = "";
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


    if (isgrdomain($sld . "." . $tld) or iseudomain($sld . "." . $tld)) {
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
    $username = '';
    $password = '';
    $apikey = encodetolatin($params["APIkey"]);
    $tld = encodetolatin($params["tld"]);
    $sld = encodetolatin($params["sld"]);


    $json = new Services_JSON();
    $jsonarray = array(
        "request" => array(
            "do" => 'getauthocode',
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
    $username = '';
    $password = '';
    $apikey = encodetolatin($params["APIkey"]);
    $tld = encodetolatin($params["tld"]);
    $sld = encodetolatin($params["sld"]);
    $nameserver = encodetolatin($params["nameserver"]);
    $ipaddress = encodetolatin($params["ipaddress"]);


    $json = new Services_JSON();
    $jsonarray = array(
        "request" => array(
            "do" => 'registerns',
            "username" => $username,
            "password" => $password,
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
    $username = '';
    $password = '';
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
            "username" => $username,
            "password" => $password,
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