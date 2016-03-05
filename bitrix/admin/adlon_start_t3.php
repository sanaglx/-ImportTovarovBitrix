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

<h2>Tовары импорта не связанные с моими</h2>
<?php
$pr=3;
//Показать товары импорта не связанные с моими
if ($pr == 3) {
    echo"<h2> Еще не готово .</h2>";
}





require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
