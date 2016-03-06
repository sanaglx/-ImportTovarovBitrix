<?php
   global $MESS; $strPath2Lang = str_replace("\\", "/", __FILE__);
     $strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php")); 
     include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php")); 
     include($strPath2Lang."/install/version.php");
     
Class adlon_import extends CModule
{
	var $MODULE_ID = "adlon.import";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $PARTNER_NAME;
	var $PARTNER_URI;


	function adlon_import()
	{
		$arModuleVersion = array();
		include(dirname(__FILE__)."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		
		$this->MODULE_NAME = GetMessage("adlon.ymlimport_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("adlon.ymlimport_MODULE_DESC");

		$this->PARTNER_NAME = GetMessage("adlon.ymlimport_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("adlon.ymlimport_PARTNER_URI");
	}
	
	public function InstallDB() {
		
		RegisterModule($this->MODULE_ID);
	}

	public function UnInstallDB() {
	
		UnRegisterModule($this->MODULE_ID);
	}
	

	function InstallFiles()
	{

		CopyDirFiles(
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/adlon.import/install/catalog_import/",
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/catalog_import",
			true, true
		);
		
		return true;
	}

	function UnInstallFiles()
	{
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/adlon.import/install/catalog_import/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/catalog_import");
		return true;
	}

	function DoInstall()
	{
		global $APPLICATION;

		if (!IsModuleInstalled("adlon.import"))
		{

			$this->InstallFiles();
			
			$this->InstallDB();
				
		}
	}

	function DoUninstall()
	{
		$this->UnInstallFiles();
		
		$this->UnInstallDB();
	}
}
?>