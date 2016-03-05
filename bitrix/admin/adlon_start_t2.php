<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);
$APPLICATION->SetTitle(GetMessage("MCART_IMPORT_XLS_STEP_0"));
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
global $DB;
$db_type = strtolower($DB->type);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/adlon.import/fat.php");

$obj = new ImportTovarov;
$obj->avtoriZ();
?>

<h2>ВЕСЬ ИМПОРТ</h2>

<?php


   $obj->perebor_Tovar(0, 22);





require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");