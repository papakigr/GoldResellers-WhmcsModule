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
    "Name" => "Natural Person",
    "LangVar" => "eunaturalperson",
    "Type" => "text",
    "Size" => "60",
    "Default" => "",
    "Required" => true,
    "Description" => $_LANG['eunaturalpersondescription']
);

$additionaldomainfields[".eu"][] = array(
    "Name" => "Citizenship",
    "LangVar" => "eucitizenship",
    "Type" => "text",
    "Size" => "60",
    "Default" => "",
    "Required" => false,
    "Description" => $_LANG['eucitizenshipdescription']
);


