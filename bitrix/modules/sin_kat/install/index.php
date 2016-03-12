<?php


Class Sin_kat extends CModule
{
    var $MODULE_ID = "sin_kat";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;

    function dull()
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
        $this->MODULE_NAME = "Синхронизация каталогов ";
        $this->MODULE_DESCRIPTION = "Синхронизация каталогов 1С с внутренней структурой сайта";
    }

    function DoInstall()
    {
        global $DOCUMENT_ROOT, $APPLICATION;
        // Install events
        RegisterModuleDependences("iblock","OnAfterIBlockElementUpdate","sin_kat","cMainSin_kat","onBeforeElementUpdateHandler");
        RegisterModule($this->MODULE_ID);
        $APPLICATION->IncludeAdminFile("Установка модуля sin_kat", $DOCUMENT_ROOT."/bitrix/modules/sin_kat/install/step.php");
        return true;
    }

    function DoUninstall()
    {
        global $DOCUMENT_ROOT, $APPLICATION;
        UnRegisterModuleDependences("iblock","OnAfterIBlockElementUpdate","dull","cMainSin_kat","onBeforeElementUpdateHandler");
        UnRegisterModule($this->MODULE_ID);
        $APPLICATION->IncludeAdminFile("Деинсталляция модуля Sin_kat", $DOCUMENT_ROOT."/bitrix/modules/dull/install/unstep.php");
        return true;
    }
}