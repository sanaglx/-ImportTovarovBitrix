<title>IMPORT_YML_6_0</title>
<?php
IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/adlon.import/import_setup_templ.php');

require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/adlon.import/classes/general/6.0/ymlimport60.php");


$ymlimport = new CYmlImport60();

$strImportErrorMessage = $ymlimport->ImportYML($DATA_FILE_NAME, $IBLOCK_ID, $IMPORT_CATEGORY, $improvproperty, $CONNECTIONS, $ONLY_PRICE, $max_execution_time, $CUR_FILE_POS, $IMPORT_CATEGORY_SECTION, $URL_DATA_FILE2, $ID_SECTION, $CAT_FILTER_I, $price_modifier);


if (!$ymlimport->AllLinesLoaded)
{
    $SETUP_VARS_LIST = "DATA_FILE_NAME, IBLOCK_ID, IMPORT_CATEGORY, improvproperty, CONNECTIONS, ONLY_PRICE, max_execution_time, CUR_FILE_POS, IMPORT_CATEGORY_SECTION, URL_DATA_FILE2, ID_SECTION, CAT_FILTER_I, price_modifier";
    
    $CUR_FILE_POS = $ymlimport->FILE_POS;
    $bAllDataLoaded = false; 
    
}


?>