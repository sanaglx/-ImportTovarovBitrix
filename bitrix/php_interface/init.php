<?php //include (add_version_01.php); ?>
<?php
AddEventHandler("main", "OnBuildGlobalMenu", "OnBuildGlobalMenuH");

function OnBuildGlobalMenuH(&$aGlobalMenu, &$aModuleMenu)
{
    foreach ($aModuleMenu as $key => $arMenu)
    {
     if ($arMenu["parent_menu"] == "global_menu_settings")
     {    
      $aModuleMenu[] = array(
          "parent_menu" => "global_menu_settings",
          "section" => "panel_admin",
          "sort" => 100,  // Сортировка
          "text" => "Админ. панель",  //Название пункта меню
          "title" => "Настройка вывода админ. панели",  // Всплывающая подсказка
          "icon" => "statistic_icon_sites",
          "page_icon" => "statistic_page_sites",
          "items_id" => "panel_admin_id",
          "url" => "/bitrix/admin/panel_admin.php?lang=ru", //Путь к странице
          );
      break;
     }
    }
}
?>
<?php

function custom_mail($to,$subject,$body,$headers) {
$f=fopen($_SERVER["DOCUMENT_ROOT"]."/maillog.txt", "a+");
fwrite($f, print_r(array('TO' => $to, 'SUBJECT' => $subject, 'BODY' => $body, 'HEADERS' => $headers),1)."\n========\n");
fclose($f);
return mail($to,$subject,$body,$headers);
}

//$iPhone = stripos($_SERVER['HTTP_USER_AGENT'], 'iPhone');
//$Android = stripos($_SERVER['HTTP_USER_AGENT'], 'Android');
//$Palmpre = stripos($_SERVER['HTTP_USER_AGENT'], 'webOS');
//$BlackBerry = stripos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry');
//$iPod = stripos($_SERVER['HTTP_USER_AGENT'], 'iPod');
//global $APPLICATION;
//$VISITOR_ID = $APPLICATION->get_cookie("MOBILE_VISITOR_MB"); 
 
//if (($iPhone || $Palmpre || $Android || $BlackBerry || $iPod) &&
//	($_SERVER["SERVER_NAME"] == "www.lapababy.ru" || $_SERVER["SERVER_NAME"] == "https://www.lapababy.ru") &&
//	empty($VISITOR_ID)) {
//		$APPLICATION->set_cookie("MOBILE_VISITOR_MB", "MOBILE", time()+60*60);
//		LocalRedirect("https://www.lapababy.ru/m/");
//		exit(); 
//	}

/**
* Autoload
*/
CModule::AddAutoloadClasses(
    '',
    array(
		'ImportTovarov' => '/bitrix/php_interface/develop/ib_to_txt.php',
    )
);

/**
* Пересчет торговых предложений 
* 
*/
AddEventHandler("catalog", "OnBeforePriceUpdate", array("MyClass", "OnBeforePriceUpdateHandler"));
class MyClass
{
   function OnBeforePriceUpdateHandler($PRICE_ID, $arFields)
   {                  
      $db_price = CPrice::GetList(
         array(),
         array(
            "ID" => $PRICE_ID
         )
      );
      
       if($ar_price = $db_price->Fetch())
      {
         if($ar_price['PRICE'] != $arFields['PRICE'])
         {

        }  
           //$ob= new UpdateTorgPredlog();
            UpdateTorgPredlog::PriceUpdateElement_($ar_price[PRODUCT_ID],$arFields['PRICE']);
      }
   }    
}

//OnAfterIBlockElementUpdate-------------------------------------------------------------------------
//AddEventHandler("iblock", "OnAfterIBlockElementUpdate", Array("UpdateTorgPredlog", "OnBeforeElement")); 
 
class UpdateTorgPredlog
{ 
 
  
  function PriceUpdateElement_($id,$pricex){
        $price_t = $pricex;
 
           $IBLOCK_ID = 10; 
            $ID = $id; 
            $arInfo =  CCatalogSKU::GetInfoByProductIBlock($IBLOCK_ID); 
            if (is_array($arInfo)) 
            { 
                 $rsOffers = CIBlockElement::GetList(
                    array(),
                    array(
                        'IBLOCK_ID' => $arInfo['IBLOCK_ID'],
                        'PROPERTY_'.$arInfo['SKU_PROPERTY_ID'] => $ID)
                        );
                        
                 while ($arOffer = $rsOffers->GetNext()) 
                { 

                AddMessage2Log( " ИД-- ".$arOffer["ID"]);
                $price_n = UpdateTorgPredlog:: Nacenka($arOffer["ID"]);
                $price_r = $price_t + $price_n;
                UpdateTorgPredlog::Ustanovi_cenu($arOffer["ID"] , $price_r);

                } 
          }
  
  }
  
  
  function OnBeforeElement(&$arFields)
    {
        $id=$arFields[ID];
        $price_n = UpdateTorgPredlog::Nacenka($id);
        //берем цену только с каталога
        if ($arFields["IBLOCK_ID"] == 10) {
            $price_t = UpdateTorgPredlog:: Pricex($id);
        }
           
           $IBLOCK_ID = 10; 
            $ID = $arFields[ID]; 
            $arInfo =  CCatalogSKU::GetInfoByProductIBlock($IBLOCK_ID); 
            if (is_array($arInfo)) 
            { 
                 $rsOffers = CIBlockElement::GetList(
                    array(),
                    array(
                        'IBLOCK_ID' => $arInfo['IBLOCK_ID'],
                        'PROPERTY_'.$arInfo['SKU_PROPERTY_ID'] => $ID)
                        );
                        
                 while ($arOffer = $rsOffers->GetNext()) 
                { 

               // AddMessage2Log( " ИД-- ".$arOffer["ID"]);
                $price_n = UpdateTorgPredlog:: Nacenka($arOffer["ID"]);
                $price_r = $price_t + $price_n;
                UpdateTorgPredlog::Ustanovi_cenu($arOffer["ID"] , $price_r);

                } 
          }
    } 
    
        
        /**
     * Здесь цена
     * @param $id
     * @return int
     */
    function Pricex($id)
    {
        $PRICE_TYPE_ID = 1;
        $PROD_ID = $id;
        $rsPrices = CPrice::GetList(array(), array('PRODUCT_ID' => $PROD_ID, 'CATALOG_GROUP_ID' => $PRICE_TYPE_ID));
        if ($arPrice = $rsPrices->Fetch()) {
            $cn = $arPrice["PRICE"];
            return $cn;
        } else {
            //если нет цены то 0
            return 0;
        }
    }

    /**
     * здесь выбираем наценку
     * @param $id
     * @return array
     */
    function Nacenka($id)
    {
        $VALUES = array();
        $BRAND_ID = $id;
        $res = CIBlockElement::GetProperty(11, $BRAND_ID, "asc", "", array("CODE" => "PRICE_PLUSUEM"));
        if ($ob = $res->Fetch()) {
            $VALUES = $ob['VALUE'];
        }
     
        return $VALUES;
    }

    /**
     * Установка цены в торговые предложения
     * @param $id ид товара
     * @param $price_r новая цена с наценкой
     */
    function Ustanovi_cenu($id, $price_r)
    {

        $PRODUCT_ID = $id;
        $PRICE_TYPE_ID = 1;

        $arFields = Array(
            "PRODUCT_ID" => $PRODUCT_ID,
            "CATALOG_GROUP_ID" => $PRICE_TYPE_ID,
            "PRICE" => $price_r,
            "CURRENCY" => "RUB",
            "QUANTITY_FROM" => 1,
            "QUANTITY_TO" => 10
        );

        $res = CPrice::GetList(
            array(),
            array(
                "PRODUCT_ID" => $PRODUCT_ID,
                "CATALOG_GROUP_ID" => $PRICE_TYPE_ID
            )
        );
        
        if ($arr = $res->Fetch()) {
          $dd =  CPrice::Update($arr["ID"], $arFields);
          if($dd){
           //   AddMessage2Log("Цена ид -".$arr["ID"]." ЗАПИСАНО в ИД-".$dd. " ЦЕНА С БАЗЫ-".$arr["PRICE"]." ПЕРЕДАЕМ-".$arFields["PRICE"]." ID-".$arFields["PRODUCT_ID"]);
          }else{
             AddMessage2Log("Продукт ид -".$arr["ID"]." ЗАПИСАТЬ НЕ УДАЛОСЬ");
          }
            
        } else {
            CPrice::Add($arFields);
       }

      }

    function print_r1($pr,$text="")
    {
        echo "<pre>".$text." :";
          print_r($pr);
        echo "</pre>";
    }
}