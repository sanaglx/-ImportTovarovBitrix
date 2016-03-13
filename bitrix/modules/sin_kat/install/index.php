<?php
/**
 * Внимание при анистале удаляюься свойства а с ними и данные в этом свойстве
 */
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));

IncludeModuleLangFile(__FILE__);

Class Sin_kat extends CModule
{
    var $MODULE_ID = "sin_kat";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;

    function __construct()
    {
        $arModuleVersion = array();
        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path."/version.php");

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
        {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }
        $this->MODULE_NAME ="Синхронизация каталогов ";
        //$this->MODULE_DESCRIPTION =GetMessage( 'SIN_KAT_MODULE_DESC');
        $this->MODULE_DESCRIPTION = "Синхронизация каталогов 1С с внутренней структурой сайта";
        $this->MODULE_CSS = "/bitrix/modules/sin_kat/styles.css";
    }

    
    function InstallFiles($arParams = array())
    {
            if($_ENV["COMPUTERNAME"]!='BX')
            {
                CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sin_kat/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");   
            }

            return true;
    }
    
    function UnInstallFiles()
    {
            if($_ENV["COMPUTERNAME"]!='BX')
            {
                   DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sin_kat/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
            }
            return true;
    }
    
    function DoInstall()
    {
      /*  if($this->isversionD7()){
            $APPLICATION->ThrowException("---D7 SYSTEM--- <br>");
        }
      */      
        global $DOCUMENT_ROOT, $APPLICATION;
        // Install events
        RegisterModuleDependences("iblock","OnAfterIBlockElementUpdate","sin_kat","cMainSin_kat","onBeforeElementUpdateHandler");
        RegisterModule($this->MODULE_ID);
        
        //$this->InstallEvents();
        $this->InstallFiles(array());
        
        $APPLICATION->IncludeAdminFile("Установка модуля sin_kat", $DOCUMENT_ROOT."/bitrix/modules/sin_kat/install/step.php");
        return true;
    }

    function DoUninstall()
    {
        global $DOCUMENT_ROOT, $APPLICATION;
        UnRegisterModuleDependences("iblock","OnAfterIBlockElementUpdate","sin_kat","cMainSin_kat","onBeforeElementUpdateHandler");
        UnRegisterModule($this->MODULE_ID);
        
        $this->UnInstallFiles();
        
        $APPLICATION->IncludeAdminFile("Деинсталляция модуля Sin_kat", $DOCUMENT_ROOT."/bitrix/modules/sin_kat/install/unstep.php");
        return true;
    }
}