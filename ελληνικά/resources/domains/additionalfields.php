<?php
/*
 **********************************************************************
 *         Additional Domain Fields          *
 **********************************************************************
*/

// .gr
$additionaldomainfields[".gr"][] = array("Name" => "Company Title" ,"LangVar"=>"grcompanytitle", "Type" => "text", "Size" => "60", "Default" => "", "Required" => false, "Description" => $_LANG['grcompanytitledescription'] );
$additionaldomainfields[".com.gr"][] = array("Name" => "Company Title" ,"LangVar"=>"grcompanytitle", "Type" => "text", "Size" => "60", "Default" => "", "Required" => false, "Description" => $_LANG['grcompanytitledescription'] );
$additionaldomainfields[".net.gr"][] = array("Name" => "Company Title" ,"LangVar"=>"grcompanytitle", "Type" => "text", "Size" => "60", "Default" => "", "Required" => false, "Description" => $_LANG['grcompanytitledescription'] );
$additionaldomainfields[".org.gr"][] = array("Name" => "Company Title" ,"LangVar"=>"grcompanytitle", "Type" => "text", "Size" => "60", "Default" => "", "Required" => false, "Description" => $_LANG['grcompanytitledescription'] );
$additionaldomainfields[".edu.gr"][] = array("Name" => "Company Title" ,"LangVar"=>"grcompanytitle", "Type" => "text", "Size" => "60", "Default" => "", "Required" => false, "Description" => $_LANG['grcompanytitledescription'] );
$additionaldomainfields[".gov.gr"][] = array("Name" => "Company Title" ,"LangVar"=>"grcompanytitle", "Type" => "text", "Size" => "60", "Default" => "", "Required" => false, "Description" => $_LANG['grcompanytitledescription'] );



$additionaldomainfields[".eu"][] = array(
    "Name" => "Citizenship",
    "LangVar" => "eucitizenship",
    "Type" => "dropdown",
    "Size" => "60",
    "Default" => "",
    "Options" => ",AT|Austria,BE|Belgium,BG|Bulgaria,CY|Cyprus,CZ|Czechia,DE|Germany,DK|Denmark,EE|Estonia,ES|Spain,FI|Finland,FR|France,GB|Great Britain,GR|Greece,HR|Croatia,HU|Hungary,IE|Ireland,IT|Italy,LT|Lithuania,LU|Luxembourg,LV|Latvia,MT|Malta,NL|Netherlands,PL|Poland,PT|Portugal,RO|Romania,SE|Sweden,SI|Slovenia,SK|Slovakia",
    "Required" => false,
    "Description" => $_LANG['eucitizenshipdescription']
);



//replace existing Entity Type
$additionaldomainfields['.eu'][] = [
    'Name' => 'Entity Type',
    'LangVar' => 'euTldEntityType',
    'Type' => 'dropdown',
    'Default' => 'INDIVIDUAL|Individual',
    'Options' =>
        'COMPANY|Company,INDIVIDUAL|Individual',

    'Description' => 'EURid Geographical Restrictions. In order to register a .EU domain '
        . 'name, you must meet certain eligibility requirements.',
];