<?
/* ��� ����� ������� �����. � ���� ���������� ��� ���������. 
 * ����� ���� YML � ������ ��� � �������
 */

IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/adlon.import/import_setup_templ.php');

global $USER;
global $APPLICATION;

define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/log.txt");


class CYmlImport60
{
	var $NUM_FIELDS = 0;
    var $bTmpUserCreated = false;
    var $strImportErrorMessage = "";
    var $strImportOKMessage = "";
    var $max_execution_time = 0;
	var $price_modifier = 1.0;
    var $AllLinesLoaded = true;
    var $FILE_POS=0;
    var $params = Array(); //������ ������� (���� ��� offers)
    
    var $fp;

    function file_get_contents($filename)
    {
        $fd = fopen("$filename", "rb");
        $content = fread($fd, filesize($filename));
        fclose($fd);
        return $content;
    }


    function CSVCheckTimeout($max_execution_time)
    {
        return ($max_execution_time <= 0) || (getmicrotime()-START_EXEC_TIME <= $max_execution_time);
    }


    // �������� xml-������
    // ����� � ���, ��� ������ ���������� ��������� �����. ������ ������������ utf-8
    // ������� ��������� ���� ��� ����, ���� �� ������ ��������� - ������
    // ���� �� ��������� - ������������ � ����� �������.
    // �� �� ���� � ��� �� ��������, � ��� �� ���� ��� ������
    function GetXMLObject($FilePath)
    {
        // ����������
        $file_content = file_get_contents ($FilePath);

        // �������� �������� ������
        $xml =  simplexml_load_string($file_content);
        if (!is_object($xml->shop))
        {
            //�� ���� ������� ������
            $file_content = iconv("UTF-8", "Windows-1251", $file_content);

            // ��������� �����������
            // ��� �����
            $xml =  simplexml_load_string($file_content);
        }

        return $xml;
    }


    // � ��� ������ ������� ��� ��� ��� ������
    function ImportYML ($DATA_FILE_NAME, $IBLOCK_ID, $IMPORT_CATEGORY, $improvproperty, $CONNECTIONS, $ONLY_PRICE, $max_execution_time, $CUR_FILE_POS,$IMPORT_CATEGORY_SECTION, $URL_DATA_FILE2, $ID_SECTION, $CAT_FILTER_I, $price_modifier)
    {

        if (!isset($USER) || !(($USER instanceof CUser) && ('CUser' == get_class($USER))))
        {
            $bTmpUserCreated = true;
            if (isset($USER))
            {
                $USER_TMP = $USER;
                unset($USER);
            }

            $USER = new CUser();
        }


        if ($max_execution_time <= 0)
            $max_execution_time = 0;
        if (defined('BX_CAT_CRON') && true == BX_CAT_CRON)
            $max_execution_time = 0;

        if (defined("CATALOG_LOAD_NO_STEP") && CATALOG_LOAD_NO_STEP)
            $max_execution_time = 0;

        $bAllLinesLoaded = true;

		if (strlen($URL_DATA_FILE) > 0)
        {
            $URL_DATA_FILE = Rel2Abs("/", $URL_DATA_FILE);
            if (file_exists($_SERVER["DOCUMENT_ROOT"].$URL_DATA_FILE) && is_file($_SERVER["DOCUMENT_ROOT"].$URL_DATA_FILE))
                $DATA_FILE_NAME = $URL_DATA_FILE;
        }
		
		if (!(strlen($DATA_FILE_NAME) > 0))
        {
		     $DATA_FILE_NAME = $URL_DATA_FILE2;
		
		}

        //if (strlen($DATA_FILE_NAME) <= 0)
        //   $strImportErrorMessage .= GetMessage("CATI_NO_DATA_FILE")."<br>";
        $IBLOCK_ID = intval($IBLOCK_ID);
        if ($IBLOCK_ID <= 0)
        {
            $strImportErrorMessage .= GetMessage("CATI_NO_IBLOCK")."<br>";
        }
        else
        {
            $arIBlock = CIBlock::GetArrayByID($IBLOCK_ID);
            if (false === $arIBlock)
            {
                $strImportErrorMessage .= GetMessage("CATI_NO_IBLOCK")."<br>";
            }
        }


        if (strlen($strImportErrorMessage) <= 0)
        {
            $bIBlockIsCatalog = false;
            if (CCatalog::GetByID($IBLOCK_ID))
                $bIBlockIsCatalog = true;

            //����� �������� �������� xml �����

            $xml;


            if (file_exists($_SERVER["DOCUMENT_ROOT"].$DATA_FILE_NAME))
            {
                $xml = $this->GetXMLObject($_SERVER["DOCUMENT_ROOT"].$DATA_FILE_NAME);
            }
			else{
			
				$uf=file_get_contents($URL_DATA_FILE2);
				//file_put_contents($_SERVER["DOCUMENT_ROOT"]."/upload/file_for_import.xml",$uf);
				$handle = fopen($_SERVER["DOCUMENT_ROOT"]."/upload/file_for_import.xml", 'w+');
				fwrite($handle, $uf);
				fclose($handle);
				$DATA_FILE_NAME="/upload/file_for_import.xml";
				$xml = $this->GetXMLObject($_SERVER["DOCUMENT_ROOT"].$DATA_FILE_NAME);
			
			}

            

            if (!is_object($xml->shop))
            {
                $strImportErrorMessage .= GetMessage("CICML_INVALID_FILE")."<br>";
            }

        }


        if (strlen($strImportErrorMessage) <= 0)
        {
		
			set_time_limit(0);
		
            $arPriceType = Array();

            //���������� ��������� �� yml �����

            $ResCatArr=array();//���� ���� ���������� ��������� ��� ����������� ��������� ��� ������ ���������

            $CategiriesList=$xml->shop->categories->category;

            foreach($CategiriesList as $Categoria){

                $CATEGORIA_XML_ID = 'yml_'.$Categoria['id'];
                






















                    if ($IMPORT_CATEGORY_SECTION=='Y'){
                
                        $CATEGORIA_PARENT_XML_ID = $Categoria['parentId'] ? 'yml_'.$Categoria['parentId'] : $ID_SECTION;
                
                    }else{
                        $CATEGORIA_PARENT_XML_ID = $Categoria['parentId'] ? 'yml_'.$Categoria['parentId'] : 0;//���� �������� �� ������ - ����� �������� ���� � ������
                    }//���� �������� �� ������ - ����� �������� ���� � ������
				
				
				
                $CATEGORIA_NAME = iconv('utf-8',SITE_CHARSET,$Categoria);

                //����, ���������� �� ����� ��������� �� �����

                $find_section_res=CIBlockSection::GetList(array(), Array("IBLOCK_ID"=>$IBLOCK_ID, "XML_ID"=>$CATEGORIA_XML_ID),false, array("ID"),false);
                if($find_section_res2=$find_section_res->GetNext()){
                    $ResCatArr[''.$CATEGORIA_XML_ID.''] = $find_section_res2["ID"];
					
					
					if ($ResCatArr[''.$CATEGORIA_XML_ID.'']==0 && $IMPORT_CATEGORY_SECTION=='Y'){
				
						$ResCatArr[''.$CATEGORIA_XML_ID.'']=$ID_SECTION;
				
					}


                    $bs = new CIBlockSection;
                    $arFields = Array(
                        "ACTIVE" => "Y",
                        "IBLOCK_ID" => $IBLOCK_ID,
                        "NAME" => $CATEGORIA_NAME,
                        "IBLOCK_SECTION_ID"=>$ResCatArr[''.$CATEGORIA_PARENT_XML_ID.''],
                        "XML_ID"=>$CATEGORIA_XML_ID,
                        //  "CODE"=>$section_code,
                    );



                    if ($IMPORT_CATEGORY=='Y'){
                        $bs->Update($find_section_res2["ID"],$arFields);
                    }

                }
                else
                {
                    //��������
					
					if ($ResCatArr[''.$CATEGORIA_PARENT_XML_ID.'']==0 && $IMPORT_CATEGORY_SECTION=='Y'){
				
						$ResCatArr[''.$CATEGORIA_PARENT_XML_ID.'']=$ID_SECTION;
				
					}

                    $section_code=CUtil::translit($CATEGORIA_NAME, LANGUAGE_ID, array(
                        "max_len" => 50,
                        "change_case" => 'U', // 'L' - toLower, 'U' - toUpper, false - do not change
                        "replace_space" => '_',
                        "replace_other" => '_',
                        "delete_repeat_replace" => true,
                    ));
                    if(preg_match('/^[0-9]/', $section_code))
                        $section_code = '_'.$section_code;


                    $bs = new CIBlockSection;
                    $arFields = Array(
                        "ACTIVE" => "Y",
                        "IBLOCK_ID" => $IBLOCK_ID,
                        "NAME" => $CATEGORIA_NAME,
                        "IBLOCK_SECTION_ID"=>$ResCatArr[''.$CATEGORIA_PARENT_XML_ID.''],
                        "XML_ID"=>$CATEGORIA_XML_ID,
                        "CODE"=>'yml_'.$section_code,
                    );



                    if ($IMPORT_CATEGORY=='Y'){
                        $ResCatArr[''.$CATEGORIA_XML_ID.'']= $bs->Add($arFields);

                        if(!$ResCatArr[''.$CATEGORIA_XML_ID.''])
                            echo $bs->LAST_ERROR;
                    }
                }
            }

 /*******************************************************************************************************************/
                			//��������� ��� ������ $connect
                			//$connect[$code] - ��� ��������� ��������
                			//$connect[$code]["code"] - ��� �������� ��������
                			//$connect[$code]["type"] - ��� �������� ��������
                            //$connect[$code]["value"] - �� �������� ��������
                            //$connect[$code]["list"] - ������ �������� ��� �������� ���� L
                                if($improvproperty):
                                    $connect = Array(); //��������� ������ ����������� 
                                    //�.�.���� ����������� ����� ������� ���������� �� ������������� ��������� ���� ��� ������� 
                                    $createnew = false; //�������� � ������������ ������ � ������� ����������
                                    
                                    if($CONNECTIONS)
                                    {
                                        $CONNECTIONS = array_reverse($CONNECTIONS);
                                         foreach($CONNECTIONS as $k => $v)
                                         {
                                            if(!($k % 3))
                                                $cd = $v;
                                            elseif($k % 3 == 2){
                                                $CONNECTION[$cd]["value"] = $v;
                                            }else {
                                                $CONNECTION[$cd]["type"] = $v;
                                            }
                                         }
                                         $CONNECTIONS = $CONNECTION;
                                         unset($CONNECTION);
                                        foreach($CONNECTIONS as $i => $cc)
                                        {
                                                $code = $i; 
                                                
                                                $connect[$code]["code"] = $code;
                                                
                                                $connect[$code]["list"] = Array();//�������� ������� ��� ������ ���� ����
                                                
                                                //������������� ��� �� ����� ���
                                                if($cc["type"] != '')                                       
                                                    $connect[$code]["type"] = $cc["type"];
                                                else
                                                    $connect[$code]["type"] = "S";  
                                                        
                                                $prpId = $cc["value"]; //�� ��������

                                                //1)���� �������� ��� ����������
                                                if($prpId > 0)
                                                {
                                                    //������������� �����
                                                    $properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$IBLOCK_ID, "ID" => $prpId));
                                                    if($prop_fields = $properties->GetNext())
                                                    {
                                                        
                                                        $connect[$code]["code"] = $prop_fields["CODE"];                                     
                                                        $connect[$code]["type"] = $prop_fields["PROPERTY_TYPE"];                                            
                                                        $connect[$code]["value"] = $prop_fields["ID"];
                                                        
                                                        //���� ����, �� ��������� ������
                                                        if($prop_fields["PROPERTY_TYPE"] == "L")
                                                        {
                                                            $db_enum_list = CIBlockProperty::GetPropertyEnum($realcode, Array(), Array("IBLOCK_ID"=>$IBLOCK_ID));
                                                            while($ar_enum_list = $db_enum_list->GetNext())
                                                            {
                                                                $connect[$code]["list"][] = $ar_enum_list["VALUE"];
                                                            }
                                                            
                                                        }
                                                        
                                                    }  
                                                }else{
                                                    //2)����� ��������, �� ������ ��������                                          
                                                    //��������� ���� �� ��� �������� � ����� �����, ���� ����, �� ������ ��� ������ ��� 
                                                    $arrCode = array();
                                                    $properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$IBLOCK_ID));
                                                    while($prop_fields = $properties->GetNext())
                                                    {
                                                        if(!in_array($prop_fields["CODE"],$arrCode))
                                                            $arrCode[] = $prop_fields["CODE"];
                                                    }
                                                    
                                                    $newCode = $connect[$code]["code"];
                                                    
                                                    if(in_array($newCode, $arrCode))
                                                        $newCode = $connect[$code]["code"]."_";
                                                    
                                                    while(in_array($newCode, $arrCode))
                                                      $newCode .= "_";
                                                    $connect[$code]["code"] = $newCode;
                                                    
                                                }
                                                    
                                         }
                                    }else $createnew = true; //�������� � �� �������� ��� ���� ��� ��������� �� ����, ���� �������� ����� �������
                                endif;
                        
                                        
/*******************************************************************************************************************/                   
            

            
            $offerlists = $xml->shop->offers->offer;

            $SITE_ID = 'ru';
            $dbSite = CSite::GetByID($SITE_ID);
            if (!$dbSite->Fetch())
            {
                $dbSite = CSite::GetList($by="sort", $order="desc");
                $arSite = $dbSite->Fetch();
                $SITE_ID = $arSite['ID'];
            }

            $tmpid = md5(uniqid(""));
            $arCatalogs = Array();
            $arCatalogsParams = Array();


            $ib = new CIBlock;
            $res = CIBlock::GetList(Array(), Array("=TYPE" => $IBLOCK_TYPE_ID, "IBLOCK_ID"=>$IBLOCK_ID, 'CHECK_PERMISSIONS' => 'Y', 'MIN_PERMISSION' => 'W'));

            if(!$res)
            {
                $strImportErrorMessage .= str_replace("#ERROR#", $ib->LAST_ERROR, str_replace("#NAME#", "[".$IBLOCK_ID."] \"".$IBLOCK_NAME."\" (".$IBLOCK_XML_ID.")", GetMessage("CICML_ERROR_ADDING_CATALOG"))).".<br>";
                $STT_CATALOG_ERROR++;
            }
            else
            {
                $el = new CIBlockElement();
                $arProducts = Array();
                $products = $xml->shop->offers->offer;

                print GetMessage("CET_PROCESS_GOING");
                print ("</br>");
                print (GetMessage("IMPORT_MSG1").$CUR_FILE_POS);
                print (GetMessage("IMPORT_MSG2").count($xml->shop->offers->offer));
                print (GetMessage("IMPORT_MSG3"));
                for ($j = $CUR_FILE_POS; $j < count($xml->shop->offers->offer); $j++)
                {

                    // ������������� ������� �� �������� ���������
                    $CUR_FILE_POS=$j;

                    $xProductNode = $xml->shop->offers->offer[$j];

                    $PRODUCT_XML_ID = "yml_".$xProductNode['id'];

                    $PRODUCT_TYPE = $xProductNode['type'];

                    // ��������� ��� ������ � �������� ��� ��������
                    switch ($PRODUCT_TYPE)
                    {
                        case "vendor.model":
                            $PRODUCT_NAME_UNCODED = $xProductNode->vendor." ".$xProductNode->model;
                            break;

                        case "book":
                        case "audiobook":
                            $PRODUCT_NAME_UNCODED = $xProductNode->author." ".$xProductNode->name;
                            break;

                        case "artist.title":
                        $PRODUCT_NAME_UNCODED = $xProductNode->artist." ".$xProductNode->title;
                            break;

                        default:
                        $PRODUCT_NAME_UNCODED = $xProductNode->name;
                    }
                    
                                      
                    $PRODUCT_NAME=iconv('utf-8',SITE_CHARSET, trim($PRODUCT_NAME_UNCODED));
                    $det_text=$xProductNode->description;
                    
                    $is_import_by_filter=false;
                    $import_by_filter=array();
                    if (!empty($CAT_FILTER_I)){
                        $import_by_filter=explode(',',$CAT_FILTER_I);
                        $is_import_by_filter=true;
                    }
                    
                    
                    $is_filtreded=true;
                    if ($is_import_by_filter){
                        $is_filtreded=false;
                        foreach($import_by_filter as $val){
                            
                            if (strpos($PRODUCT_NAME,$val)!==false || strpos($det_text,$val)!==false){
                                $is_filtreded=true;
                            }
                        }
                    }
                    

                    // die();
                    

                    //$fp = @fopen($_SERVER["DOCUMENT_ROOT"]."/log.txt", "a");

                    $PRODUCT_XML_CAT_ID = "yml_".$xProductNode->categoryId;

                    $ProductPrice=$xProductNode->price;
                    
                    //price changing
                    if (doubleval($price_modifier)!==1.00)
                    {
                        $ProductPrice = $ProductPrice*doubleval($price_modifier);
                    }

                    global $USER;
                    
                    if ($is_filtreded)
                    {
                    
                    
                    $arLoadProductArray = Array(
                        "MODIFIED_BY"       =>  $USER->GetID(),
                        "IBLOCK_SECTION_ID" =>  $ResCatArr["".$PRODUCT_XML_CAT_ID],
                        "IBLOCK_ID"         =>  $IBLOCK_ID,
                        "NAME"              =>  $PRODUCT_NAME,
                        "XML_ID"                =>  $PRODUCT_XML_ID,
                        "ACTIVE"=>$xProductNode['available']==true?'Y':'N',
                        "DETAIL_PICTURE" => CFile::MakeFileArray($xProductNode->picture[0]),
                        "PREVIEW_PICTURE" => CFile::MakeFileArray($xProductNode->picture[0]),
                        "DETAIL_TEXT"=>iconv('utf-8',SITE_CHARSET,$xProductNode->description),
                        // �������� ��� ������
                        "CODE" => CUtil::translit($PRODUCT_NAME, 'ru', array()),
                    );


                    $res = CIBlockElement::GetList(Array(), Array("IBLOCK_ID"=>$IBLOCK_ID, "XML_ID"=>$PRODUCT_XML_ID));
                    $bNewRecord_tmp = False;

                    // ������ ��� ��� ������
                    $flag_ok = 0;

                    $PRODUCT_ID=false;
                    // ����� ��� ����?
                    if ($arr = $res->Fetch())
                    {
                        $PRODUCT_ID = $arr["ID"];

                        if ($ONLY_PRICE!='Y')
                        {
                            // ���������
                            $flag_ok = $el->Update($PRODUCT_ID, $arLoadProductArray);
                            //fwrite($fp, "already was. updated ".$PRODUCT_XML_ID." ".$PRODUCT_NAME."\n");

                            // ��� ���� ����� ���
                            if (!$flag_ok)
                            {
                                // �� ����� ��� ��. ��������
                                $arLoadProductArray["CODE"] = $arLoadProductArray["XML_ID"];
                                // ��� ��� ��������
                                $flag_ok = $el->Update($PRODUCT_ID, $arLoadProductArray);
                              //fwrite($fp, "code changed to xmlid ".$PRODUCT_XML_ID." ".$PRODUCT_NAME."\n");
                            }

                        }
                    }
                    // ������ ������ ��� �� ����
                    else
                    {
                        if ($ONLY_PRICE!='Y')
                        {
                            // ���������
                            $flag_ok=false;
                            $PRODUCT_ID  = $el->Add($arLoadProductArray);
                            if ($PRODUCT_ID) $flag_ok=true;
                            //fwrite($fp, "new record ".$PRODUCT_XML_ID." ".$PRODUCT_NAME."\n");

                            // �� ���������! ����� ��� ��� ����
                            if (!$flag_ok)
                            {
                                // ��������
                                $arLoadProductArray["CODE"] = $arLoadProductArray["XML_ID"];
                                // ��� ��� ��������
                                $PRODUCT_ID = $el->Add($arLoadProductArray);
                                if ($PRODUCT_ID) $flag_ok=true;
                              //  fwrite($fp, "code changed to xmlid ".$PRODUCT_XML_ID." ".$PRODUCT_NAME."\n");
                            }

                        }

                    }

                    if ($flag_ok)
                    {
                                            
                        $arFieldsProduct = array
                        (
                            "ID" => $PRODUCT_ID,
                            "QUANTITY" => 10,
                            "CAN_BUY_ZERO" => "Y"
                        );
                        CCatalogProduct::Add($arFieldsProduct);


                        //��������� ������� ���� ��� ������
                        $price_ok=CPrice::SetBasePrice($PRODUCT_ID,$ProductPrice,"RUB");

                       // if ($price_ok) print "���� ������� ���������<br>";

                        if ($ONLY_PRICE!='Y')
                        {

                            $PROPERTY_VALUE=array();
                            $count=0;
                            if (isset ($xOfferListNode))
                            {
                                if($improvproperty):
                                    foreach($xOfferListNode->param as $param)
                                    {
                                        //print $param['name']."<br>";
                                        $PROPERTY_VALUE['n'.$count]=array('VALUE'=>iconv('utf-8',SITE_CHARSET,$param),'DESCRIPTION'=>iconv('utf-8',SITE_CHARSET,$param['name']));
                                        $count++;
                                        
                                        $param_name = iconv('utf-8',SITE_CHARSET,$param['name']);                                   
                                        $transParams = array("replace_space" => "_", "replace_other" => "_");
                                        $code = strtoupper(Cutil::translit($param_name, "ru", $transParams));                                   

                                        
                                        /**************************�������� ��� offers *************************************/   
                                        if(!in_array($code,$params)){                                       
                                            $params[] = $code;
                                            //�.�.���� �� ���� � ��������� �������� � ��������                                      
                                                        
                                            $PropID = intVal($connect[$code]["value"]); 
            
                                            if($PropID == 0)
                                            {
                                                
    											if($createnew)
    											{
    												$connect[$code]["code"] = $code;										
    												$connect[$code]["type"] = "S"; 
    												//���� �� ���� ������ ������� ��������� � ������� ������ (������� ����� �������)
    												//���� �� ���� ��������
    												$properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$IBLOCK_ID, "CODE" => $code));
    												if($prop_fields = $properties->GetNext())
    												{
    													$PropID = $prop_fields["ID"];	
    													$connect[$code]["type"] = $prop_fields["PROPERTY_TYPE"]; 											
    													$connect[$code]["value"] = $prop_fields["ID"];
    													$connect[$code]["list"] = array();
    													
    												}
    											}
    											
    											if($PropID == 0){ //���� ��� ������ ��������, ��������� �����
    											
    												$arFields = Array(
    												  "NAME" => $param_name,
    												  "ACTIVE" => "Y",
    												  "SORT" => "100",
    												  "CODE" => $connect[$code]["code"],
    												  "PROPERTY_TYPE" => $connect[$code]["type"],
    												  "IBLOCK_ID" => $IBLOCK_ID
    												  );
    												  										
    												$ibp = new CIBlockProperty;
    												$PropID = $ibp->Add($arFields);
    												$connect[$code]["value"] = $PropID;
    											}
    										}
    									}
    									/****************************************************************/
    									//����� ������� ��������� ����� ���� ����������. 
    									//���� ��� �������� � ������ �� ��������� ���	  
    									if($connect[$code]["type"] == "L")
    									{
    										if($param != "")
    										{															
    											if(!in_array(iconv('utf-8',SITE_CHARSET,$param), $connect[$code]["list"]))
    											{
    												$connect[$code]["list"][] = iconv('utf-8',SITE_CHARSET,$param);
    												
    												$cnt = 0;
    												$ar_all_values = Array();
    												$db_enum_list = CIBlockProperty::GetPropertyEnum($connect[$code]["value"], Array('SORT'=>'ASC'));
    												
    												//����� ��� ������� ���� ��������� �� ������� � �����. 
    												$addparam = true;
    												while($ar_enum = $db_enum_list->Fetch())
    												{
    												    $cnt++;
    												    $ar_all_values[$ar_enum['ID']] = Array('SORT'=>$cnt, 'VALUE'=>$ar_enum['VALUE']);
    												    if(iconv('utf-8',SITE_CHARSET,$param) == $ar_enum['VALUE'])
    												    	$addparam = false;
    												}
    												if($addparam)
    												{
    													$ar_all_values[] = Array("SORT" =>$cnt,"VALUE" =>iconv('utf-8',SITE_CHARSET,$param));
    													$CIBlockProp = new CIBlockProperty;
    													$CIBlockProp->UpdateEnum($connect[$code]["value"], $ar_all_values);
    												}
    												
    											}
    										} 
    										$db_enum_list = CIBlockProperty::GetPropertyEnum($code, Array(), Array("IBLOCK_ID"=>$IBLOCK_ID, "VALUE"=>iconv('utf-8',SITE_CHARSET,$param)));
    										if($ar_enum_list = $db_enum_list->GetNext())
    										     $param = $ar_enum_list["ID"]; 
    										
    									} 
    									//�.�.���������� �������� �������� � �����									
    									$t = CIBlockElement::SetPropertyValueCode($PRODUCT_ID, $code, iconv('utf-8',SITE_CHARSET, $param)); 
    									
    									
    								}
                                else:
                                    foreach($xOfferListNode->param as $param)
                                      {
                                          //print $param['name']."<br>";
                                          $PROPERTY_VALUE['n'.$count]=array('VALUE'=>iconv('utf-8',SITE_CHARSET,$param),'DESCRIPTION'=>iconv('utf-8',SITE_CHARSET,$param['name']));
                                           $count++;

                                       }
                                endif;
							}
							
							if (isset ($xProductNode))
							{
								foreach($xProductNode->param as $param){

	//                              print $param['name']."<br>";
									$PROPERTY_VALUE['n'.$count]=array('VALUE'=>iconv('utf-8',SITE_CHARSET,$param),'DESCRIPTION'=>iconv('utf-8',SITE_CHARSET,$param['name']));
									$count++;
									if($improvproperty):
    									$param_name = iconv('utf-8',SITE_CHARSET,$param['name']);									
    									$transParams = array("replace_space" => "_", "replace_other" => "_");
    									$code = strtoupper(Cutil::translit($param_name, "ru", $transParams));
    									
    									
    									
    									/**************************�������� ��� �������� ������ *************************************/	
    									if(!in_array($code,$params)) //��������� ����� ��������
    									{
    										   		$params[] = $code;
    										  			
    										        $PropID = intVal($connect[$code]["value"]);	
    										  		   	
    									         	//��������� � ���������� ����� ��������
    																				
    												$properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), 
    													Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$IBLOCK_ID, "CODE" => $connect[$code]["code"]));
    													
    												while ($prop_fields = $properties->GetNext())
    												{
    												  $PropID = $prop_fields["ID"];
    												}
    												
    												if($PropID == 0)
    												{
    													if($createnew)
    													{
    														$connect[$code]["code"] = $code;										
    														$connect[$code]["type"] = "S"; 
    														//���� �� ���� ������ ������� ��������� � ������� ������ (������� ����� �������)
    														//���� �� ���� ��������
    														$properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$IBLOCK_ID, "CODE" => $code));
    														if($prop_fields = $properties->GetNext())
    														{
    															$PropID = $prop_fields["ID"];	
    															$connect[$code]["type"] = $prop_fields["PROPERTY_TYPE"]; 											
    															$connect[$code]["value"] = $prop_fields["ID"];
    															$connect[$code]["list"] = array();
    															
    														}
    													}
    											
    													if($PropID == 0) //���� ��� ������ ��������, ��������� �����
    													{
    													
    															$arFields = Array(
    															  "NAME" => $param_name,
    															  "ACTIVE" => "Y",
    															  "SORT" => "100",
    															  "CODE" => $connect[$code]["code"],
    															  "PROPERTY_TYPE" => $connect[$code]["type"],
    															  "IBLOCK_ID" => $IBLOCK_ID
    															  );
    															  										
    															$ibp = new CIBlockProperty;
    															$PropID = $ibp->Add($arFields);
    															$connect[$code]["value"] = $PropID;
    															
    													}
    												}
    										
    									}
    									/***************************************************************/
    									
    									
    									//����� ������� ��������� ����� ���� ����������. 
    									//���� ��� �������� � ������ �� ��������� ���	
    									
    									if($connect[$code]["type"] == "L")
    									{
    										if($param != "")
    										{
    															
    											if(!in_array(iconv('utf-8',SITE_CHARSET,$param), $connect[$code]["list"]))
    											{
    												$connect[$code]["list"][] = iconv('utf-8',SITE_CHARSET,$param);
    												
    												$cnt = 0;
    												$ar_all_values = Array();
    												//����� ��� ������� ���� ��������� �� ������� � �����. 
    												$db_enum_list = CIBlockProperty::GetPropertyEnum($connect[$code]["value"], Array('SORT'=>'ASC'));
    												$addparam = true;
    												while($ar_enum = $db_enum_list->Fetch())
    												{
    												    $cnt++;
    												    $ar_all_values[$ar_enum['ID']] = Array('SORT'=>$cnt, 'VALUE'=>$ar_enum['VALUE']);
    												    if(iconv('utf-8',SITE_CHARSET,$param) == $ar_enum['VALUE'])
    												    	$addparam = false;
    												}
    												if($addparam)
    												{
    													$ar_all_values[] = Array("SORT" =>$cnt,"VALUE" =>iconv('utf-8',SITE_CHARSET,$param));
    													$CIBlockProp = new CIBlockProperty;
    													$CIBlockProp->UpdateEnum($connect[$code]["value"], $ar_all_values);
    												}
    												
    											}
    											
    										} 
    										$db_enum_list = CIBlockProperty::GetPropertyEnum($connect[$code]["code"], Array(), Array("IBLOCK_ID"=>$IBLOCK_ID, "VALUE"=>iconv('utf-8',SITE_CHARSET,$param)));
    										if($ar_enum_list = $db_enum_list->GetNext())
    										     $param = $ar_enum_list["ID"]; 
    										
    									} 
    									//�.�.���������� �������� �������� � �����  
    									$t = CIBlockElement::SetPropertyValueCode($PRODUCT_ID, $connect[$code]["code"], iconv('utf-8',SITE_CHARSET,$param)); 
    								endif;
								}
							}

                            $ELEMENT_ID = $PRODUCT_ID;  // ��� ��������
                            $PROPERTY_CODE = "CML2_ATTRIBUTES";  // ��� ��������
                            //$PROPERTY_VALUE = $prop_array;  // �������� ��������

                            // ��������� ����� �������� ��� ������� �������� ������� ��������
                            CIBlockElement::SetPropertyValuesEx($ELEMENT_ID, $IBLOCK_ID, array($PROPERTY_CODE=>$PROPERTY_VALUE));

                            //���������� ������� - ���� �� �������� ��������, ���� ���, �� ��������� � ��������
                            //���������� �������� ��������
                            
                        }

                    }
                    else
                    {
                        echo "\nError: ".$el->LAST_ERROR."\n";
                        echo $PRODUCT_XML_ID." ".$PRODUCT_NAME."\n\n";
                       // fwrite($fp, "here was error ".$PRODUCT_XML_ID." ".$PRODUCT_NAME."\n");
                    }
					
					}

                    

                    // ���� ������ ����������, $bAllLinesLoaded = false
                    if (!($bAllLinesLoaded = $this->CSVCheckTimeout($max_execution_time))) break;

                }

            }
        }
//fclose($fp);
        // �� ������ ��������� �� �������
        if (!$bAllLinesLoaded)
        {
            // ����������� �������
            $CUR_FILE_POS++;
            $this->FILE_POS = $CUR_FILE_POS;
            // ������ ��� ���� ���������������
            $this->AllLinesLoaded = false;

        }


        if ($bTmpUserCreated)
        {

            unset($USER);
            if (isset($USER_TMP))
            {
                $USER = $USER_TMP;
            }
            unset($USER_TMP);
        }
        return $strImportErrorMessage;
    }
}
?>
