<?if(!check_bitrix_sessid()) return;?>
<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/sin_kat/classes/general/cMainSin_kat.php";
$obj = new cMainSin_kat();
$zn = $obj->delProp();
echo "<br> Удалено свойство для блока 10 :";

$zn = $obj->delProp("id_post", 11);
echo "<br> Удалено  свойство для блока 11 ";


echo CAdminMessage::ShowNote("Модуль успешно удален из системы");
?>