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

<h2>Перенос остатков торг предложений</h2>

<?php
    $ar1 = $obj->readNomTo(23);
    foreach ($ar1 as $kl => $zn) {
        $zn2 = $obj->search_Torg($id_blok = 11, $property = 543, $kl, $zn);
        $sm = $sm + $zn2[0];
        $klv = $klv + 1;
    }
    echo"<br> Всего изменено -" . $sm . " товаров из -" . $klv . "<br>";
    // echo "<pre>"; print_r($ar1); echo "</pre>";




require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");