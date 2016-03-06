
<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/bitrix/modules/main/include/prolog_before.php");
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
// echo "<pre>"; print_r($ar2); echo "</pre>";

$obj = new ImportTovarov;
$obj->avtoriZ();
/*
//$obj->torg_Predlog_poisk(1409,10);
$ar1 = $obj->readNomTo(23);
	foreach($ar1 as $kl => $zn){
	   $zn2 = $obj->search_Torg($id_blok=11,$property=543,$kl,$zn);
	   $sm=$sm+$zn2[0];
	   $klv=$klv+1;
	}
	echo"<br> Всего изменено -".$sm." товаров из -".$klv."<br>";
*/	
$obj-> torg_Predlog(1408,10);
