<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);
$APPLICATION->SetTitle(GetMessage("MCART_IMPORT_XLS_STEP_0"));
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
global $DB;
$db_type = strtolower($DB->type);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/adlon.import/fat.php");
require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/adlon.import/classes/general/ib_to_imp.php");
//-------------------------------------------------------------------------
//НАСТРОЙКИ ИСПОЛЬЗУЕМЫЕ С МОДУЛЯ
$idIblokCatalog = COption::GetOptionString('adlon.import', 'idIblokCatalog');
$idIblokPredlog = COption::GetOptionString('adlon.import', 'idIblokPredlog');
$idIblokCatalogImp = COption::GetOptionString('adlon.import', 'idIblokCatalogImp');
$idIblokPredlogImp = COption::GetOptionString('adlon.import', 'idIblokPredlogImp');
$nomPropertyCatalog = COption::GetOptionString('adlon.import', 'nomPropertyCatalog');
$nomPropertyPredlog = COption::GetOptionString('adlon.import', 'nomPropertyPredlog');

//--------------------------------------------------------------------------
//НАСТРОЙКА ЗДЕСЬ для перехода на настройки модуля закоментируйте местные настройки
$idIblokCatalog = 10;       // Каталог
$idIblokPredlog = 11 ;      // Торговые предложения
$idIblokCatalogImp = 22;    // Импорт Каталог
$idIblokPredlogImp= 23;     // Импорт Торговые предложения
$nomPropertyCatalog = 464;  //№ Свойтво по которому связываем каталоги
$nomPropertyPredlog  = 543; //№  Свойтво по которому связываем торговые предложения  

//--------------------------------------------
$obj = new ImportTovarov;
$obj->avtoriZ();
?>
<style>
    .simple-little-table {
	font-family:Arial, Helvetica, sans-serif;
	color:#666;
	font-size:12px;
	text-shadow: 1px 1px 0px #fff;
	background:#eaebec;
	margin:20px;
	border:#ccc 1px solid;
	border-collapse:separate;
 
	-moz-border-radius:3px;
	-webkit-border-radius:3px;
	border-radius:3px;
 
	-moz-box-shadow: 0 1px 2px #d1d1d1;
	-webkit-box-shadow: 0 1px 2px #d1d1d1;
	box-shadow: 0 1px 2px #d1d1d1;
}
 
.simple-little-table th {
	font-weight:bold;
	padding:5px 25px 3px 25px;
	border-top:1px solid #fafafa;
	border-bottom:1px solid #e0e0e0;
 
	background: #ededed;
	background: -webkit-gradient(linear, left top, left bottom, from(#ededed), to(#ebebeb));
	background: -moz-linear-gradient(top,  #ededed,  #ebebeb);
}
.simple-little-table th:first-child{
	text-align: left;
	padding-left:20px;
}
.simple-little-table tr:first-child th:first-child{
	-moz-border-radius-topleft:3px;
	-webkit-border-top-left-radius:3px;
	border-top-left-radius:3px;
}
.simple-little-table tr:first-child th:last-child{
	-moz-border-radius-topright:3px;
	-webkit-border-top-right-radius:3px;
	border-top-right-radius:3px;
}
.simple-little-table tr{
	text-align: center;
	padding-left:20px;
}
.simple-little-table tr td:first-child{
	text-align: left;
	padding-left:20px;
	border-left: 0;
}
.simple-little-table tr td {
	padding:5px;
	border-top: 1px solid #ffffff;
	border-bottom:1px solid #e0e0e0;
	border-left: 1px solid #e0e0e0;
 
	background: #fafafa;
	background: -webkit-gradient(linear, left top, left bottom, from(#fbfbfb), to(#fafafa));
	background: -moz-linear-gradient(top,  #fbfbfb,  #fafafa);
}
.simple-little-table tr:nth-child(even) td{
	background: #f6f6f6;
	background: -webkit-gradient(linear, left top, left bottom, from(#f8f8f8), to(#f6f6f6));
	background: -moz-linear-gradient(top,  #f8f8f8,  #f6f6f6);
}
.simple-little-table tr:last-child td{
	border-bottom:0;
}
.simple-little-table tr:last-child td:first-child{
	-moz-border-radius-bottomleft:3px;
	-webkit-border-bottom-left-radius:3px;
	border-bottom-left-radius:3px;
}
.simple-little-table tr:last-child td:last-child{
	-moz-border-radius-bottomright:3px;
	-webkit-border-bottom-right-radius:3px;
	border-bottom-right-radius:3px;
}
.simple-little-table tr:hover td{
	background: #f2f2f2;
	background: -webkit-gradient(linear, left top, left bottom, from(#f2f2f2), to(#f0f0f0));
	background: -moz-linear-gradient(top,  #f2f2f2,  #f0f0f0);
}
 
.simple-little-table a:link {
	color: #666;
	font-weight: bold;
	text-decoration:none;
}
.simple-little-table a:visited {
	color: #999999;
	font-weight:bold;
	text-decoration:none;
}
.simple-little-table a:active,
.simple-little-table a:hover {
	color: #bd5a35;
	text-decoration:underline;
}
.tdleft{
    padding-left: 35px !important;
}
font.text{
    font-size: 1.1em;
    margin: 1px;
    line-height: 2.5;
} 


</style>

<div class= "adon_1" style = "">
   <div style="float:left; margin:3px;">
   <a id="filtrn" class='adm-btn' href='/bitrix/admin/adlon_start.php?lang=ru&pr=1'>Показать ВСЕ мои товары</a>
   </div>
   <div style="float:left; margin:3px;">
    <a id="filtry" class='adm-btn' href='/bitrix/admin/adlon_start.php?lang=ru&pr=2'>Показать ВЕСЬ Импорт</a>
    </div>
   
   <div style="float:left; margin:3px;">
    <a id="filtrn" class='adm-btn' href='/bitrix/admin/adlon_start.php?lang=ru&pr=3'>Показать товары импорта не связанные с моими</a>
    </div>
   <div style="float:left; margin:3px;">
    <a id="filtrn" class='adm-btn' href='/bitrix/admin/adlon_start.php?lang=ru&pr=4'>Показать мои товары не связанные с импортом</a>
    </div>
   
   <div style="float:left; margin:3px;">
    <a class='adm-btn' href='/bitrix/admin/adlon_start.php?lang=ru&pr=5'>Выполнить перенос остатков торг предложений</a>
    </div>
   <div style="float:left; margin:3px;">
    <a class='adm-btn' href='/bitrix/admin/adlon_start.php?lang=ru&pr=6'>Выполнить перенос остатков товаров</a>
    </div>
   <div style="float:left; margin:3px;">
    <a class='adm-btn' href='/bitrix/admin/adlon_start.php'>К оглавлению</a></li>
</div>

</div>
<div style="border:1px solid #CCC;">
<table width="100%" border="0" style="padding:10px;" cellspacing="0" cellpadding="0">
  <tr>
    <td><form action="/bitrix/admin/adlon_start.php?lang=ru&pr=7" method="POST">
    <label>Нацека </label><input name="chislo" value="0" type="text">
    <select name="nac_vyb">
        <option selected value="1">Сумма</option>
        <option value="0">Процентов</option>
    </select>
    <button class="adm-btn ">Выполнить перенос сумм торг предложений</button>
</form>
<br>
<form action="/bitrix/admin/adlon_start.php?lang=ru&pr=8" method="POST">
    <label>Нацека </label><input name="chislo" value="0" type="text">
    <select name="nac_vyb">
        <option selected value="1">Сумма</option>
        <option value="0">Процентов</option>
    </select>
    <button class="adm-btn ">Выполнить перенос сумм товаров</button>
</form></td>
  </tr>
</table>
</div>


<?php
//Показать ВСЕ мои товары
$pr = $_GET['pr'];
if ($pr == 1) {
    $obj->perebor_Tovar(0, $idIblokCatalog);
}

//Показать ВЕСЬ Импорт
if ($pr == 2) {
    $obj->perebor_Tovar(0, $idIblokCatalogImp);
}

//Показать товары импорта не связанные с моими
if ($pr == 3) {
    //echo"<h2> Еще не готово .</h2>";
    $obj->perebor_Tovarxx(0, $idIblokCatalogImp, $idIblokCatalog, $nomPropertyCatalog, $idIblokPredlog, $nomPropertyPredlog);
}

//Показать мои товары не связанные с импортом
if ($pr == 4) {
   // echo"<h2> Еще не готово </h2>.";
    $obj->filterTovarPredlImp($idIblokCatalog);
}

//Выполнить перенос остатков
if ($pr == 5) {
    //echo"<h2> Еще не готово .</h2>";
    echo"Формат отображения 1500/5 <br> 1500 это ID товара, 5 это количество товара<br><br>";
    //$obj->torg_Predlog_poisk(1409,10);
    $ar1 = $obj->readNomTo($idIblokPredlogImp);
    foreach ($ar1 as $kl => $zn) {
        $zn2 = $obj->search_Torg($id_blok = $idIblokPredlog, $property = $nomPropertyPredlog, $kl, $zn);
        $sm = $sm + $zn2[0];
        $klv = $klv + 1;
    }
    echo"<br> Всего изменено -" . $sm . " товаров из -" . $klv . "<br>";
    // echo "<pre>"; print_r($ar1); echo "</pre>";
}

//инфоблоки нашего каталога
if ($pr == 6) {
    //echo"<h2> Еще не готово .</h2>";
     echo"Формат отображения 1500/5 <br> 1500 это ID товара, 5 это количество товара<br><br>";
    //$obj->torg_Predlog_poisk(1409,10);
    $ar1 = $obj->readNomTo($idIblokCatalogImp);
    foreach ($ar1 as $kl => $zn) {
        $zn2 = $obj->search_Torg($id_blok = $idIblokCatalog, $property = $nomPropertyCatalog, $kl, $zn);
        $sm = $sm + $zn2[0];
        $klv = $klv + 1;
    }
    echo"<br> Всего изменено -" . $sm . " товаров из -" . $klv . "<br>";
    // echo "<pre>"; print_r($ar1); echo "</pre>";
}

//Выполнить перенос сумм
if ($pr == 7) {
  //  echo"<h2> Еще не готово .</h2>";
    $klv=1;
    if($_POST["nac_vyb"]){
         $nacenka=$_POST["chislo"];
         echo"<h2> Наценка на сумму:".$nacenka." rub</h2>";
    }else{
         $nacenka=$_POST["chislo"];
         echo"<h2> Наценка:".$nacenka."%</h2>";
    }
    
    $ar1 = $obj->readNomTo($idIblokPredlogImp);
       foreach ($ar1 as $kl => $zn) {
        $zn2 = $obj->upd_Pricex($id_blok=$idIblokPredlog, $property=$nomPropertyPredlog, $kl, $nacenka, $_POST["nac_vyb"]);
        $zn3=$zn3+$zn2;
        $klv = $klv + 1;
       
    }
   echo"<br> Операция завершеа обработано строк -" . $klv . "<br> Изменено:".$zn3;
   
}

//Выполнить перенос сумм
if ($pr == 8) {
   // echo"<h2> Еще не готово .</h2>";
    $klv=1;
    if($_POST["nac_vyb"]){
         $nacenka=$_POST["chislo"];
         echo"<h2> Наценка на сумму:".$nacenka." rub</h2>";
    }else{
         $nacenka=$_POST["chislo"];
         echo"<h2> Наценка:".$nacenka."%</h2>";
    }
    $ar1 = $obj->readNomTo($idIblokCatalogImp);
       foreach ($ar1 as $kl => $zn) {
        $zn2 = $obj->upd_Pricex($id_blok=$idIblokCatalog, $property=$nomPropertyCatalog, $kl, $nacenka, $_POST["nac_vyb"]);
        $zn3=$zn3+$zn2;
        $klv = $klv + 1;
       
    }
   echo"<br> Операция завершеа обработано строк -" . $klv . "<br> Изменено:".$zn3;
   
}



require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
