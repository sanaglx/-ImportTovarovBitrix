<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
// echo "<pre>"; print_r($ar2); echo "</pre>";

$obj = new ImportTovarov;
$obj->avtoriZ();

//$obj->torg_Predlog_poisk(1409,10);
/* $ar1 = $obj->readNomTo(23);
  foreach($ar1 as $kl => $zn){
  $zn2 = $obj->search_Torg($id_blok=11,$property=543,$kl,$zn);
  $sm=$sm+$zn2[0];
  $klv=$klv+1;
  }
  echo"<br> Всего изменено -".$sm." товаров из -".$klv."<br>";
 */
//$obj-> torg_Predlog(1408,10);
//$arInfo = CCatalogSKU::GetInfoByProductIBlock(10);
//echo "<pre> SKU_PROPERTY_ID (ID свойства привязки торговых предложений к товарам).- :"; print_r($arInfo );echo "</pre>";
//////////////////////////////////////////////////////////
//torg_Predlog_poisk5(1409,10);

function torg_Predlog_poisk5($id_tp, $id_blok = 0) {
    $intElementID = $id_tp;
    $mxResult = CCatalogSku::GetProductInfo($intElementID);
    if (is_array($mxResult)) {
        $id_tov = $mxResult['ID'];
    } else {
        ShowError('Это не торговое предложение');
    }

    $tp = CCatalogSKU::IsExistOffers($id_tov, $id_blok);

    Echo "Есть торговые предложения";
    $arInfo = CCatalogSKU::GetInfoByProductIBlock($id_blok); //каталог товаров
    echo "<pre> YYYY - :";
    print_r($arInfo);
    echo "</pre>";

    if (is_array($arInfo)) {
        $rsOffers = CIBlockElement::GetList(
                        array(), array(
                    'IBLOCK_ID' => $arInfo['IBLOCK_ID'],
                    'PROPERTY_' . $arInfo['SKU_PROPERTY_ID'] => $id_tov)
        );
        while ($arOffer = $rsOffers->GetNext()) {
            echo "<pre>Товары поргового предложения :";
            print_r($arOffer);
            echo "</pre>";
            $ar_res = CCatalogProduct::GetByIDEx($arOffer["ID"]);
//   if (strlen($ar_res['PROPERTIES']['art_post']['VALUE']) > 3) {
            echo "<pre>Параметры товара ================================:";
            echo "ID -" . $ar_res['ID'];
            echo "kol-vo -" . $ar_res['PRODUCT']['QUANTITY'];
            echo "cena 1 -" . $ar_res['PRICES'][1]['PRICE'];
            echo "cena 3 -" . $ar_res['PRICES'][3]['PRICE'];

            echo "cena 4 -" . $ar_res['PRICES'][4]['PRICE'];
            echo "vneh_kod- " . $ar_res['PROPERTIES']['art_post']['VALUE'];

            echo "</pre>";

//  }


            echo "<pre>Параметры товара------------ :";
            print_r($ar_res);
            echo "</pre>";
        }
    }
}

/////////////////////////////////////////////////////////////////    
// search_Torg5(11,543,0,0);

Function search_Torg5($id_blok = 11, $property = 543, $property_value = '0', $kol_tov = 0) {

//$arSelect = Array("ID", "NAME", "QUANTITY","DATE_ACTIVE_FROM","PROPERTY_".$property);
    $arSelect = Array("ID", "QUANTITY");
    $arFilter = Array(
        "IBLOCK_ID" => $id_blok,
        "ID" => 1409,
        //	"PROPERTY_464_VALUE" =>'11111111',
// "PROPERTY_" . $property . "_VALUE" => $property_value,
        "ACTIVE_DATE" => "Y",
        "ACTIVE" => "Y"
    );
//поиск ид товара по полю art...
    $res = CIBlockElement::GetList(
                    Array(), $arFilter, false, Array("nPageSize" => 10), false
//$arSelect
    );
    $sm = array();
    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
//    $klv = $this->upd_Kol_tp($arFields['ID'], $kol_tov);
        $sm[0] = $sm[0] + $klv;
        echo "<pre>";
        print_r($arFields);
        echo "</pre>";
    }

    return $sm;
}

////////////////////////////////////////////////////////////////////////// 
//===========================================================================    
/** Поиск по art_post  нужного торг предложения
 * 		PROPERTY_464 - это в товаре  art_post  $id_blok=10
 * 		PROPERTY_543 - это в  торгвых предложениях  $id_blok=11
 *          возращает массив идов
 *          переносит количество товара
 *   search_torg();
 */
$id_blok = 11;
$property = 543;
$property_value = 50701;

//upd_Pricex($id_blok, $property, $property_value);

function upd_Pricex($id_blok, $property, $property_value) {
    $arSelect = Array("ID");
    $arFilter = Array(
        "IBLOCK_ID" => $id_blok,
        "PROPERTY_" . $property . "_VALUE" => $property_value,
        "ACTIVE_DATE" => "Y",
        "ACTIVE" => "Y",
    );

    $res5 = CIBlockElement::GetList(
                    Array(), $arFilter, false,
                    //Array("nPageSize"=>100), 
                    false, $arSelect
    );
    $sm = array();
    while ($ob = $res5->GetNextElement()) {
        $arFields = $ob->GetFields();
        if ($arFields) {
            $PRODUCT_ID = $arFields["ID"];
        }
    }

//-----------------------------------------------------------------------------       
    $PRODUCT_ID_I = $property_value;
    $res1 = CPrice::GetList(
                    array(), array(
                "PRODUCT_ID" => $PRODUCT_ID_I
// "CATALOG_GROUP_ID" => $PRICE_TYPE_ID
                    )
    );

    while ($arr1 = $res1->Fetch()) {

        if ($arr1) {
            $arFields = Array(
                "PRODUCT_ID" => $PRODUCT_ID,
                "CATALOG_GROUP_ID" => $arr1["CATALOG_GROUP_ID"],
                "PRICE" => $arr1["PRICE"],
                "CURRENCY" => $arr1["CURRENCY"]
            );

            $res = CPrice::GetList(
                            array(), array(
                        "PRODUCT_ID" => $PRODUCT_ID,
                        "CATALOG_GROUP_ID" => $arr1["CATALOG_GROUP_ID"]
                            )
            );

            if ($arr = $res->Fetch()) {
                CPrice::Update($arr["ID"], $arFields);
            } else {
                CPrice::Add($arFields);
            }
        } else {
            echo"UPS";
            return false;
        }
    }


    echo "<pre>";
    print_r($arFields);
    echo "</pre>";
}

//Пробуем выборку из двух инфоблоков
//test4(10,22,464);

function test4($id10 = 10, $id22 = 22, $prop = 0) {
    echo"XXX";
    /*
     * Количество элемнетов во всех инфоблоках
      $res = CIBlock::GetList(
      Array(),
      Array(

      ), true
      );
      while($ar_res = $res->Fetch())
      {
      echo $ar_res['NAME'].': '.$ar_res['ELEMENT_CNT'].'<br>';
      }

     */

    $arFilter = Array(
        array("ID" => "DESC"),
        array(
            "LOGIC" => "AND",
            array("IBLOCK_ID" => 11),
            array("IBLOCK_ID" => 10, "PROPERTY_464_VALUE" => $prop)
        ),
        false,
        false,
        array()
    );

    $res = CIBlockElement::GetList($arFilter);
    while ($arFields = $res->Fetch()) {
        echo"<pre>";
        print_r($arFields);
        echo"<pre>";
    }
}

//////////////////
perebor_Tovarxx(0, 22, 10, 464, 11, 543);

function perebor_Tovarxx($ostatok = 0, $id_blok = 22, $id_blok1 = 10, $prop = 464, $id_blok2 = 11, $prop2 = 543) {

    if ($id_blok == 22) {
        echo "<h2> Товары импорта не связанные с моими</h2>";
    }

    $res24 = CIBlockElement::GetList(
                    array(), array('IBLOCK_ID' => $id_blok,
                //array( "LOGIC" => "AND",
                //    array(">ID"=>42816),
                //    array("<ID"=>42830)
                //     ),
                array(),
                    ), false, false
// array('nPageSize'=>1000),
// array("ID")    
    );
    /*
      while($res=$res24->Fetch()){

      $rsOffers = CIBlockElement::GetList(
      array(),
      array('IBLOCK_ID' => 10, 'PROPERTY_' . 464 => $res["ID"]),
      false,
      false,
      Array("ID")
      );
      if(!$arOffer = $rsOffers->Fetch()){
      echo "<pre>Параметры товара :"; print_r($res);echo "</pre>";
      }

      }
     */


    $res24->NavStart(100);
    echo $res24->NavPrint("Товары");

    $html = "<table width=80% border=1 style ='border: 1px solid #000;'>";
    $html .="<tr><th>Наименование, ссылка</th><th>куда-ID</th><th>Количество</th><th>Привязка-ID</th></tr>";
    $vsego = $res24->SelectedRowsCount();
    echo "Всего элементов каталога:" . $vsego;

    while ($rs = $res24->NavNext(true, "f_")):
// while ($rs = $res24->GetNext()):

        $id_tov1 = CCatalogProduct::GetByID($rs["ID"]);
        $id_tov = $id_tov1['ID'];
        $tp = CCatalogSKU::IsExistOffers($id_tov, $id_blok);
        $arInfo = CCatalogSKU::GetInfoByProductIBlock($id_blok); //каталог товаров
        $ar4 = CIBlockElement::GetByID($id_tov);
        $nn = $ar4->GetNext();
        if ($tp) {

            if (is_array($arInfo)) {
                $rsOffers = CIBlockElement::GetList(
                                array(), array('IBLOCK_ID' => $arInfo['IBLOCK_ID'], 'PROPERTY_' . $arInfo['SKU_PROPERTY_ID'] => $id_tov)
                );

                $html .="<tr><td colspan='4'><a href='/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=" . $id_blok . "&type=catalog&ID=" . $id_tov . "&lang=ru'>" . $nn['NAME'] . "</a></td></tr>";
                   
                while ($arOffer = $rsOffers->GetNext()) {
                $ar_res = CCatalogProduct::GetByIDEx($arOffer["ID"]);
                
                    if (strlen($ar_res['PROPERTIES']['art_post']['VALUE']) >= 0) {
                        
                        $arr4=array('IBLOCK_ID' => $id_blok2,'PROPERTY_' . $prop2 => $ar_res['ID']);
                        $rsf = CIBlockElement::GetList(array(),  $arr4 );
                         
                       if (!$ar5 = $rsf->Fetch()){
                        $html .="<tr>";
                        $html .="<td width=50%>" . $ar_res['NAME'] . "-------</td>";
                        $html .="<td width=10%>" . $ar_res['ID'] . "</td>";
                        $html .="<td width=20%>" . $ar_res['PRODUCT']['QUANTITY'] . "</td>";
                        $html .="<td width=20%>" . $ar_res['PROPERTIES']['art_post']['VALUE'] . "</td>";
                        $html .="</tr>";

                       }
                    }
                }
            }
        } else {

            $rsOffersx = CIBlockElement::GetList(
                            array(), array(
                        'IBLOCK_ID' => $id_blok1,
                        'PROPERTY_' . $prop => $rs["ID"]
                            )
            );

            if (!$arOfferx = $rsOffersx->Fetch()) {
                $rsOffers = CIBlockElement::GetList(
                                array(), array('IBLOCK_ID' => $id_blok, 'ID' => $id_tov)
                );


                while ($arOffer = $rsOffers->GetNext()) {
                    $ar_res = CCatalogProduct::GetByIDEx($arOffer["ID"]);
                    $html .="<tr>";
                    $html .="<td width=50%><a href='/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=" . $id_blok . "&type=catalog&ID=" . $id_tov . "&lang=ru'>" . $ar_res['NAME'] . "</a></td>";
                    $html .="<td width= 10%>" . $ar_res['ID'] . "</td>";
                    $html .="<td width=20%>" . $ar_res['PRODUCT']['QUANTITY'] . "</td>";
                    $html .="<td width=20%>" . $ar_res['PROPERTIES']['art_post']['VALUE'] . "</td>"; //PROP[464][2358:464]							
                    $html .="</tr>";
                }
            }
//здесь если нет торговых предложений
        }

//echo "<pre>Параметры товара :"; print_r($ar_res);echo "</pre>";

    endwhile;

    $html .="</table>";
    echo $html;
}
