<?if(!check_bitrix_sessid()) return;?>
<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/sin_kat/classes/general/cMainSin_kat.php";
$obj = new cMainSin_kat();
$zn = $obj->addProp();
echo "<br> Создано свойство для блока 10 :". $zn;

$zn = $obj->addProp("ID_Поставщика", 11);
echo "<br> Создано свойство для блока 11 :". $zn;

echo CAdminMessage::ShowNote("Модуль Sin_kat установлен");
?>