<?php

/**
 * Переброска количества после импорта товаров
 * SanaGL 02.2016
 */
Class ImportTovarov {
    /** 
     *  Получить id товара  для переноса,возвращает массив номеров товаров 
     */
    Function readNomTo($id_blok) {
        $arNomTo = array();
            // $arOrder = array("ID" => "DEST");
            //$arSubQuery = array("IBLOCK_ID" => $id_blok);
            // внесем свойство по которому будем фильтровать и его значение 
            //делаем сам подзапрос
            //$arFilter['ID'] = CIBlockElement::SubQuery('свойство привязки предложения к товару', $arSubQuery); 
            //дальше делаем запрос  в $arFilter  будут только те id где в привязанных предложениям есть размер 38
        $res = CIBlockElement::GetList(
            array("ID" => "DEST"),
            array("IBLOCK_ID" => $id_blok),
            false,
            false,
            array()
        );

        $i = 0; 

        While ($ar = $res->Fetch()) {
            $aa = CCatalogProduct::GetByID($ar['ID']);
            $arNomTo[$aa['ID']] = $aa['QUANTITY'];
            $i = $i + 1;
         }
			
        return $arNomTo;
    }
   
    /** разработка
     *  Получить id торгового предложения  для переноса, возвращает массив торгового предложения
     */
    Function readNomTp() {
        $db_res = CIBlockElement::GetList(
                        Array(), Array("IBLOCK_ID" => 22), false, false, array()
        );

        //$db_res = CCatalogProduct::GetList( array(), $arFilter, false, false, array());
         
        $i = 0;
        While ($ar = $db_res->GetNext()) {

            echo "<pre>"; print_r($ar); echo "</pre>";
            //echo $ar["ID"]."<br>";
            $i = $i + 1;

            $res = CCatalogSKU::getOffersList($ar);
            echo "<pre>===="; print_r($res); echo "</pre>";

            if ($i == 5) {
                exit();
            }
        }

        return $arNomTp;
    }

    /*     * выбирает из торговых предложений количество и внешний код товара  по торг предлож.
     *   torg_Predlog(1409,10);
     */
    function torg_Predlog_poisk($id_tp, $id_blok = 0) {
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
        echo "<pre> YYYY - :"; print_r($arInfo); echo "</pre>";

        if (is_array($arInfo)) {
            $rsOffers = CIBlockElement::GetList(
                            array(), array('IBLOCK_ID' => $arInfo['IBLOCK_ID'], 'PROPERTY_' . $arInfo['SKU_PROPERTY_ID'] => $id_tov)
                            // array('IBLOCK_ID' => $arInfo['IBLOCK_ID']) //пройти по всем товарам (долго)
            );
            while ($arOffer = $rsOffers->GetNext()) {
                echo "<pre>Товары поргового предложения :";
                print_r($arOffer);
                echo "</pre>";
                $ar_res = CCatalogProduct::GetByIDEx($arOffer["ID"]);
                if (strlen($ar_res['PROPERTIES']['art_post']['VALUE']) > 3) {
                    echo "<pre>Параметры товара :";
                    echo "ID -" . $ar_res['ID'];
                    echo "kol-vo -" . $ar_res['PRODUCT']['QUANTITY'];
                    echo "vneh_kod- " . $ar_res['PROPERTIES']['art_post']['VALUE'];
                    echo "</pre>";
                }


                echo "<pre>Параметры товара :"; print_r($ar_res);  echo "</pre>";
            }
        }
    }

    /** выбирает из торговых предложений количество и внешний код товара
     *   torg_Predlog(1408,10);
     * ecли есть торговые предложения  PROP[543][1409:543] -- art_post
     * SUBCAT_BASE_QUANTITY -- доступное количество
     * [PRODUCT][QUANTITY] =>10
     * [PROPERTIES][art_post][VALUE] => 44444
     */
    function torg_Predlog($id_tov, $id_blok = 0) {

        $tp = CCatalogSKU::IsExistOffers($id_tov, $id_blok);
        if ($tp) {
            Echo "Есть торговые предложения";
            $arInfo = CCatalogSKU::GetInfoByProductIBlock($id_blok); //каталог товаров
            $ar4 = CIBlockElement::GetByID($id_tov);
            $nn = $ar4->GetNext();
            //echo "<pre> YYYY - :"; print_r($nn );echo "</pre>";
   /*           [IBLOCK_ID] => 11
                [PRODUCT_IBLOCK_ID] => 10
                [SKU_PROPERTY_ID] => 58
                [VERSION] => 2
*/
            if (is_array($arInfo)) {
                $rsOffers = CIBlockElement::GetList(
                                array(), array('IBLOCK_ID' => $arInfo['IBLOCK_ID'], 'PROPERTY_' . $arInfo['SKU_PROPERTY_ID'] => $id_tov)
                                // array('IBLOCK_ID' => $arInfo['IBLOCK_ID']) //пройти по всем товарам (долго)
                );


                $html = "<table  class='simple-little-table'  cellspacing='0'>";
                $html .="<tr><th>Наименование</th><th>ID товара</th><th>Количество</th><th>Привязка-ID</th></tr>";
                $html .="<tr><td colspan='4'><a target='_blank' href='/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=" . $id_blok . "&type=catalog&ID=" . $id_tov . "&lang=ru'>" . $nn['NAME'] . "</a></td></tr>";

                while ($arOffer = $rsOffers->GetNext()) {
                    $ar_res = CCatalogProduct::GetByIDEx($arOffer["ID"]);
                    if (strlen($ar_res['PROPERTIES']['art_post']['VALUE']) > 3) {
                        $html .="<tr>";
                        $html .="<td>" . $ar_res['NAME'] . "</td>";
                        $html .="<td>" . $ar_res['ID'] . "</td>";
                        $html .="<td>" . $ar_res['PRODUCT']['QUANTITY'] . "</td>";
                        $html .="<td>" . $ar_res['PROPERTIES']['art_post']['VALUE'] . "</td>";
                        $html .="</tr>";
                    }



                    //echo "<pre>Параметры товара :"; print_r($ar_res);echo "</pre>";
                    //print_r1($ar_res,"Параметры товара");
                }
                $html .="</table>";
                echo $html;
            }
        } else {
            Echo "Нет торг. предложений";
            //здесь если нет торговых предложений
        }
    }

    /** выбирает из торговых предложений количество и внешний код товара
     *   perebor_Tovar(0,10);
     *   возвращает таблицу всех товаров и торг предложений
     */
    function perebor_Tovar($ostatok = 0, $id_blok = 0) {

        if ($id_blok == 10) {
            echo "<h2>Товары с Каталога ЛАПАБЕБИ</h2>";
        }
        if ($id_blok == 22) {
            echo "<h2>Товары с Каталога ИМПОРТА</h2>";
        }
               
        $res24 = CIBlockElement::GetList(
            array(),
            array('IBLOCK_ID' => $id_blok, 
                 array( "LOGIC" => "OR",
                    // array(">PROPERTY_QUANTITY_VALUE"=>0),
                    //array(">CATALOG_QUANTITY"=>0)
                     ),
                ),
            false,
            false
           // array('nPageSize'=>100,'iNumPage'=>1)
        );
        
        $res24->NavStart(100);
        echo $res24->NavPrint("Товары");
        
        $html = "<table width=80% class='simple-little-table'  cellspacing='0'>";
        $html .="<tr><th>Наименование, ссылка</th><th>ID товара</th><th>Количество</th><th>Привязка-ID</th></tr>";
        $vsego = $res24->SelectedRowsCount();
        echo "Всего элементов каталога:".$vsego;
	
        while($rs=$res24->NavNext(true, "f_")):
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
                                    // array('IBLOCK_ID' => $arInfo['IBLOCK_ID']) //пройти по всем товарам (долго)
                    );


                    
                    $html .="<tr><td colspan='4'><a target='_blank' href='/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=" . $id_blok . "&type=catalog&ID=" . $id_tov . "&lang=ru'>" . $nn['NAME'] . "</a></td></tr>";

                    while ($arOffer = $rsOffers->GetNext()) {
                        $ar_res = CCatalogProduct::GetByIDEx($arOffer["ID"]);
                        if (strlen($ar_res['PROPERTIES']['art_post']['VALUE']) >= 0) {
                            $html .="<tr>";
                            $html .="<td width=50% class='tdleft'>  " . $ar_res['NAME'] . "</td>";
                            $html .="<td width=10%>" . $ar_res['ID'] . "</td>";
                            $html .="<td width=20%>" . $ar_res['PRODUCT']['QUANTITY'] . "</td>";
                            $html .="<td width=20%>" . $ar_res['PROPERTIES']['art_post']['VALUE'] . "</td>";
                            $html .="</tr>";
                        }



                        //echo "<pre>Параметры товара :"; print_r($ar_res);echo "</pre>";
                    }
                 
                }
            }
            else 
                {

                $rsOffers = CIBlockElement::GetList(
                                array(), array('IBLOCK_ID' => $id_blok, 'ID' => $id_tov)
                                // array('IBLOCK_ID' => $arInfo['IBLOCK_ID']) //пройти по всем товарам (долго)
                );
                //$html = "<table width=80% border=1 style ='border: 1px solid #000;'>";
                //$html .="<tr><th>Нет торг. предложений, берем с товара</th><th>ID товара</th><th>Количество</th><th>Привязка-ID</th></tr>";
                //$html .="<tr><td colspan='4'><a href='/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=".$id_blok."&type=catalog&ID=".$id_tov."&lang=ru'>".$nn['NAME']."</a></td></tr>";
                while ($arOffer = $rsOffers->GetNext()) {
                    $ar_res = CCatalogProduct::GetByIDEx($arOffer["ID"]);
                    $html .="<tr>";
                    $html .="<td width=50% ><a target='_blank' href='/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=" . $id_blok . "&type=catalog&ID=" . $id_tov . "&lang=ru'>" . $ar_res['NAME'] . "</a></td>";
                    $html .="<td width= 10%>" . $ar_res['ID'] . "</td>";
                    $html .="<td width=20%>" . $ar_res['PRODUCT']['QUANTITY'] . "</td>";
                    $html .="<td width=20%>" . $ar_res['PROPERTIES']['art_post']['VALUE'] . "</td>"; //PROP[464][2358:464]							
                    $html .="</tr>";
                }


                //здесь если нет торговых предложений
            }

        //echo "<pre>Параметры товара :"; print_r($ar_res);echo "</pre>";

        endwhile;
        
                        $html .="</table>";
                echo $html;
    }

    //===========================================================================    
    /** Поиск по art_post  нужного торг предложения
     * 		PROPERTY_464 - это в товаре  art_post  $id_blok=10
     * 		PROPERTY_543 - это в  торгвых предложениях  $id_blok=11
     *          возращает массив идов
     *          переносит количество товара
     *   search_torg();
     */
    Function search_Torg($id_blok = 11, $property = 543, $property_value = '0', $kol_tov = 0) {

        //$arSelect = Array("ID", "NAME", "QUANTITY","DATE_ACTIVE_FROM","PROPERTY_".$property);
        $arSelect = Array("ID", "QUANTITY");
            
        // "ID"=>1409,
        //"PROPERTY_464_VALUE" =>'11111111',
        $arFilter = Array(
            "IBLOCK_ID" => $id_blok,
            "PROPERTY_" . $property . "_VALUE" => $property_value,
            "ACTIVE_DATE" => "Y",
            "ACTIVE" => "Y"
        );

        $res = CIBlockElement::GetList(
                        Array(),
                        $arFilter,
                        false,
                        //Array("nPageSize"=>100), 
                        false, 
                        $arSelect
        );
        $sm = array();
        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $klv = $this->upd_Kol_tp($arFields['ID'], $kol_tov);
            $sm[0] = $sm[0] + $klv;
        }
            //echo "<pre>"; print_r($arFields );echo "</pre>";
        return $sm;
    }

    
    //===========================================================================    
    /** Поиск по art_post  сумм и их перенос
     * 		PROPERTY_464 - это в товаре  art_post  $id_blok=10
     * 		PROPERTY_543 - это в  торгвых предложениях  $id_blok=11
     *          возращает массив идов
     *          переносит количество товара
     *   search_torg();
     */
    function upd_Pricex($id_blok=11, $property=543, $property_value, $nacenka=0, $nac_vyb=0) {
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
                $PRODUCT_ID = $arFields["ID"];  //1409
            }
         }
        $PRODUCT_ID_I = $property_value;
           $res1 = CPrice::GetList(
                        array(),
                        array("PRODUCT_ID" => $PRODUCT_ID_I
                        // "CATALOG_GROUP_ID" => $PRICE_TYPE_ID
                        )
         );
        
// удалим все предыдущие цены 
CPrice::DeleteByProduct( $PRODUCT_ID  );
$i=0;
$zp2=0;
$zp1=0;
        while ($arr1 = $res1->Fetch()) {
      
           if ($arr1) {
                $arFields = Array(
                    "PRODUCT_ID" => $PRODUCT_ID,
                    "CATALOG_GROUP_ID" => $arr1["CATALOG_GROUP_ID"],
                    "PRICE" => $arr1["PRICE"],
                    "CURRENCY" => $arr1["CURRENCY"]
                );
             

                //добавим новые
                $res = CPrice::GetList(
                                array("CATALOG_GROUP_ID"=>ASC),
                                array(
                                    "PRODUCT_ID" => $PRODUCT_ID,
                                    "CATALOG_GROUP_ID" => $arr1["CATALOG_GROUP_ID"]
                                )
                );
                    if($PRODUCT_ID >0)
                    {
                 
                        if ($arr = $res->Fetch())
                        {
                            CPrice::Update($arr["ID"], $arFields);
                            $zp2=$zp2+1;
                        } else {
                          $IDtp =  CPrice::Add($arFields);
                            $zp2=$zp2+1;
                         // echo "<pre> товар ";    print_r($arFields);    echo "</pre>";
                        }
                        
                        if($arr1["CATALOG_GROUP_ID"]==1){ $ID_ID = $IDtp; }
                        
                        //если есть наценка
                        if(!$nacenka==0 and $arr1["CATALOG_GROUP_ID"]==4)
                        {
                            if($nac_vyb)
                            {    
                                 $arFields["PRICE"] = $arr1["PRICE"] + $nacenka;
                            }else{  
                                 $arFields["PRICE"] = $arr1["PRICE"] + round(($arr1["PRICE"]*$nacenka)/100,0);
                            }   
                            $arFields["CATALOG_GROUP_ID"] = 1;
                                if($i)
                                {
                                    CPrice::Update($ID_ID, $arFields);
                                    $i=0;
                                 //   echo "<pre> товар_наценка upd";    print_r($arFields);    echo "</pre>";
                                }else{
                                    CPrice::Add($arFields); 
                                    $i=0;
                                  //  echo "<pre> товар_наценка add";    print_r($arFields);    echo "</pre>";
                                } 
                                 
                        }
                        //признак наличия базовой цены     
                        if($arr1["CATALOG_GROUP_ID"]==1) $i=1;     
                       
                    } 
               
                } else {
                     echo"UPS";
                     $arFields["error"]="UPS";
                     return false;
                 }     
              $zp1=$zp1+1;   
           } 
           
           return $zp2;
       // echo"<br>Обработано записей:".$zp1; 
       // echo"<br>Записей изменено:".$zp2;
       // echo "<pre>";    print_r($arFields);    echo "</pre>";
    }

    
    /** ----------------------------------------------------------
     *  Обновляет количество товара 
     * 	upd_Kol_tp(1409,300);
     *  рабочая функция
     */
    function upd_Kol_tp($id_tov, $kol_tov = 0) {
        Cmodule::IncludeModule('catalog');
        $PRODUCT_ID = $id_tov; // id товара
        $arFields = array('QUANTITY' => $kol_tov); //  количество товыра
        if (CCatalogProduct::Update($PRODUCT_ID, $arFields)) {
            echo $id_tov . "/" . $kol_tov . "<br>";
            return 1;
        };
        return 0;
    }

    /**
     * Выбираем с импота все товары где есть остаток на складе
     * записать выбранное в файл название товара, ид, и ссылка на товар 
     */
    Function fileImportCreate() {
        
    }

    /*
     * Авторизация
     */

    Function avtoriZ() {
        global $USER;
        global $DB;
        if ($USER->IsAdmin()) {
            
        } else {
            echo "<br>Вы не авторизованы!<br>";
            exit;
        };
    }
    
    /**
     *  filterTovarPredlImp
     */
    Function filterTovarPredlImp($id_blok=10, $property=543){
         if ($id_blok == 10) {
            echo "<h2>Товары каталога и торг.предложения  ЛАПАБЕБИ не связаные с импортом</h2>";
        }
        if ($id_blok == 22) {
            echo "<h2>Товары с Каталога ИМПОРТА</h2>";
        }
               
        $res24 = CIBlockElement::GetList(
            array(),
            array('IBLOCK_ID' => $id_blok, 
                 array( "LOGIC" => "OR",
                  //  array("=PROPERTY_" . $property . "_VALUE" => ""),
                  //  array("=PROPERTY_" . $property1 . "_VALUE" => "")
                     ),
                ),
            false,
            false
           // array('nPageSize'=>100,'iNumPage'=>1)
        );
        
        $res24->NavStart(100);
        echo $res24->NavPrint("Товары");
        
        $html = "<table width=80% class='simple-little-table'  cellspacing='0'>";
        $html .="<tr><th>Наименование, ссылка</th><th>ID товара</th><th>Количество</th><th>Привязка-ID</th></tr>";
        $vsego = $res24->SelectedRowsCount();
        echo "Всего элементов каталога:".$vsego;
	$i=0;
        while($rs=$res24->NavNext(true, "f_")):
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
                                    array(),
                                    array(
                                        'IBLOCK_ID' => $arInfo['IBLOCK_ID'],
                                        'PROPERTY_' . $arInfo['SKU_PROPERTY_ID'] => $id_tov
                                    )
                                    // array('IBLOCK_ID' => $arInfo['IBLOCK_ID']) //пройти по всем товарам (долго)
                    );


                    
                    $html .="<tr><td colspan='4'><a target='_blank' href='/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=" . $id_blok . "&type=catalog&ID=" . $id_tov . "&lang=ru'>" . $nn['NAME'] . "</a></td></tr>";

                    while ($arOffer = $rsOffers->GetNext()) {
                        $ar_res = CCatalogProduct::GetByIDEx($arOffer["ID"]);
                        if (strlen($ar_res['PROPERTIES']['art_post']['VALUE']) == 0) {
                            $html .="<tr>";
                            $html .="<td width=50%  class='tdleft'>" . $ar_res['NAME'] . "</td>";
                            $html .="<td width=10%>" . $ar_res['ID'] . "</td>";
                            $html .="<td width=20%>" . $ar_res['PRODUCT']['QUANTITY'] . "</td>";
                            $html .="<td width=20%>" . $ar_res['PROPERTIES']['art_post']['VALUE'] . "</td>";
                            $html .="</tr>";
                            $i=$i+1;
                        }



                        //echo "<pre>Параметры товара :"; print_r($ar_res);echo "</pre>";
                    }
                 
                }
            }
            else 
                {

                $rsOffers = CIBlockElement::GetList(
                                array(), array('IBLOCK_ID' => $id_blok, 'ID' => $id_tov)
                                // array('IBLOCK_ID' => $arInfo['IBLOCK_ID']) //пройти по всем товарам (долго)
                );
                //$html = "<table width=80% border=1 style ='border: 1px solid #000;'>";
                //$html .="<tr><th>Нет торг. предложений, берем с товара</th><th>ID товара</th><th>Количество</th><th>Привязка-ID</th></tr>";
                //$html .="<tr><td colspan='4'><a href='/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=".$id_blok."&type=catalog&ID=".$id_tov."&lang=ru'>".$nn['NAME']."</a></td></tr>";
                while ($arOffer = $rsOffers->GetNext()) {
                    $ar_res = CCatalogProduct::GetByIDEx($arOffer["ID"]);
                    if (strlen($ar_res['PROPERTIES']['art_post']['VALUE']) == 0) {
                        $html .="<tr>";
                        $html .="<td width=50% ><a target='_blank' href='/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=" . $id_blok . "&type=catalog&ID=" . $id_tov . "&lang=ru'>" . $ar_res['NAME'] . "</a></td>";
                        $html .="<td width= 10%>" . $ar_res['ID'] . "</td>";
                        $html .="<td width=20%>" . $ar_res['PRODUCT']['QUANTITY'] . "</td>";
                        $html .="<td width=20%>" . $ar_res['PROPERTIES']['art_post']['VALUE'] . "</td>"; //PROP[464][2358:464]							
                        $html .="</tr>";
                        $i=$i+1;
                    }
                }


                //здесь если нет торговых предложений
            }

        //echo "<pre>Параметры товара :"; print_r($ar_res);echo "</pre>";

        endwhile;
        
                $html .="</table>";
                echo "Выведено записей:".$i;
                echo $html;
        
    } 
    
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

    $html = "<table width=80%  class='simple-little-table'  cellspacing='0'>";
    $html .="<tr><th>Наименование, ссылка</th><th>ID товара</th><th>Количество</th><th>Привязка-ID</th></tr>";
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

                $html .="<tr><td colspan='4'><a target='_blank' href='/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=" . $id_blok . "&type=catalog&ID=" . $id_tov . "&lang=ru'>" . $nn['NAME'] . "</a></td></tr>";
                   
                while ($arOffer = $rsOffers->GetNext()) {
                $ar_res = CCatalogProduct::GetByIDEx($arOffer["ID"]);
                
                    if (strlen($ar_res['PROPERTIES']['art_post']['VALUE']) >= 0) {
                        
                        $arr4=array('IBLOCK_ID' => $id_blok2,'PROPERTY_' . $prop2 => $ar_res['ID']);
                        $rsf = CIBlockElement::GetList(array(),  $arr4 );
                         
                       if (!$ar5 = $rsf->Fetch()){
                        $html .="<tr>";
                        $html .="<td width=50%  class=tdleft>" . $ar_res['NAME'] . "</td>";
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
                    $html .="<td width=50%><a target='_blank' href='/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=" . $id_blok . "&type=catalog&ID=" . $id_tov . "&lang=ru'>" . $ar_res['NAME'] . "</a></td>";
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
     
//end class   
}

	