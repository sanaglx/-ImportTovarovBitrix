<?

IncludeModuleLangFile(__FILE__);
$APPLICATION->SetAdditionalCSS("/bitrix/panel/main/adlon.css");

if($APPLICATION->GetGroupRight("mcart.xls")!="D"){
    $aMenu = array(
        "parent_menu" => "global_menu",
        "section" => "adlon.import",
        "sort" => 800,
		"icon" => "adlon_menu_icon",
        "text" =>  GetMessage("ADLON_EXCEL"),
        "title" =>  GetMessage("ADLON_EXCEL_IMPORT"),
        "url"  => "adlon_start.php?lang=".LANGUAGE_ID,
        
        "items_id" => "menu_mcart_xls",
		 "items"       => array()
		
    );
	
	  
	   
    return $aMenu;
	
}
return false;


?>