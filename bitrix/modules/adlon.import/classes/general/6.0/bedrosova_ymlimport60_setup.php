<title>IMPORT_YML_6_0</title>
<?
IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/adlon.import/import_setup_templ.php');

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/adlon.import/classes/general/6.0/ymlimport60.php");

global $APPLICATION, $USER;
if(isset($_REQUEST["prev_btn"]) && $_REQUEST["prev_btn"] == GetMessage("CET_PREV_STEP"))
	$STEP = 1;
$arSetupErrors = array();

$PARAMETERS;
$improvproperty;
$IBLOCK_ID;
$URL_DATA_FILE;
$DATA_FILE_NAME;
$CONNECTIONS;

if ($IMPORT_CATEGORY=='')$IMPORT_CATEGORY='Y';
$ONLY_PRICE;
$max_execution_time;
if ($price_modifier=='')$price_modifier=1.0;

$IMPORT_CATEGORY_SECTION;
$URL_DATA_FILE2;
$ID_SECTION;
$CAT_FILTER_I;

$counter =1;


if ($STEP <= 1)
{
	if (isset($arOldSetupVars['IBLOCK_ID']))
		$IBLOCK_ID = $arOldSetupVars['IBLOCK_ID'];
	if (isset($arOldSetupVars['improvproperty']))
		$improvproperty = $arOldSetupVars['improvproperty'];
	if (isset($arOldSetupVars['CONNECTIONS']))
		$CONNECTIONS = $arOldSetupVars['CONNECTIONS'];
	if (isset($arOldSetupVars['DATA_FILE_NAME']))
		$URL_DATA_FILE = $arOldSetupVars['DATA_FILE_NAME'];
	if (isset($arOldSetupVars['IMPORT_CATEGORY']))
		$IMPORT_CATEGORY = $arOldSetupVars['IMPORT_CATEGORY'];
	if (isset($arOldSetupVars['ONLY_PRICE']))
		$ONLY_PRICE = $arOldSetupVars['ONLY_PRICE'];
	if (isset($arOldSetupVars['max_execution_time']))
		$max_execution_time = $arOldSetupVars['max_execution_time'];
	if (isset($arOldSetupVars['SETUP_PROFILE_NAME']))
		$SETUP_PROFILE_NAME = $arOldSetupVars['SETUP_PROFILE_NAME'];
		
	if (isset($arOldSetupVars['IMPORT_CATEGORY_SECTION']))
		$IMPORT_CATEGORY_SECTION = $arOldSetupVars['IMPORT_CATEGORY_SECTION'];
	if (isset($arOldSetupVars['URL_DATA_FILE2']))
		$URL_DATA_FILE2 = $arOldSetupVars['URL_DATA_FILE2'];
	if (isset($arOldSetupVars['URL_DATA_FILE']))
		$URL_DATA_FILE = $arOldSetupVars['URL_DATA_FILE'];
		
	if (isset($arOldSetupVars['ID_SECTION']))
		$ID_SECTION = $arOldSetupVars['ID_SECTION'];
		
	
if (isset($arOldSetupVars['CAT_FILTER_I']))
		$CAT_FILTER_I= $arOldSetupVars['CAT_FILTER_I'];	
		
		if (isset($arOldSetupVars['price_modifier']))
		$price_modifier = $arOldSetupVars['price_modifier'];
}


	//*****************************************************************//
	// проверка перехода ко 2 вкладке
	if ($STEP == 2)
	{
		
		// должен быть файл
		if (strlen($URL_DATA_FILE) > 0 && file_exists($_SERVER["DOCUMENT_ROOT"].$URL_DATA_FILE) && is_file($_SERVER["DOCUMENT_ROOT"].$URL_DATA_FILE) && $APPLICATION->GetFileAccessPermission($URL_DATA_FILE)>="R")
			$DATA_FILE_NAME = $URL_DATA_FILE;
	
		if (strlen($DATA_FILE_NAME) <= 0 && !(strlen($URL_DATA_FILE2) > 0))
			$arSetupErrors[] = GetMessage("CATI_NO_DATA_FILE");
	
		
		$IBLOCK_ID = IntVal($IBLOCK_ID);
		$arIBlock = array();
		
		// не выбран инфоблок
		if ($IBLOCK_ID <= 0)
		{
			$arSetupErrors[] = GetMessage("CATI_NO_IBLOCK");
		}
		else
		{
			$arIBlock = CIBlock::GetArrayByID($IBLOCK_ID);
			if (false === $arIBlock)
			{
				$arSetupErrors[] = GetMessage("CATI_NO_IBLOCK");
			}
		}
		
		if (!CIBlockRights::UserHasRightTo($IBLOCK_ID, $IBLOCK_ID, 'iblock_admin_display'))
			$arSetupErrors[] = GetMessage("CATI_NO_IBLOCK_RIGHTS");
	
		$bIBlockIsCatalog = False;
		if (CCatalog::GetByID($IBLOCK_ID))
			$bIBlockIsCatalog = True;
		
		// не должно быть ошибок
		if (!empty($arSetupErrors))
		{
			// иначе остаемс€ на месте
			$STEP = 1;
		}
	}


	if (($STEP == 2)&&($improvproperty == 1))  //если выбор свойств
	{
			$CONNECTIONS = unserialize($_REQUEST["CONNECTIONS"]);
			//*********** читаем параметры выгрузки *********************************************//
			echo($URL_DATA_FILE2." - ".$DATA_FILE_NAME."<br />");
			if($URL_DATA_FILE2 != ''){
				$file = $URL_DATA_FILE2;
				if(!(strpos($file,"http://") === 0))
				 $file = "http://".$file;
			}
			else $file = $_SERVER["DOCUMENT_ROOT"].$DATA_FILE_NAME;
			$xmlFile = new CYmlImport60;
			$xml = $xmlFile->GetXMLObject($file);
			$prodNode = $xml->shop->offers->offer[0];
			
			$params = Array();
			
			foreach($prodNode->param as $param){							
				$param_name = iconv('utf-8',SITE_CHARSET,$param['name']);									
				$transParams = array("replace_space" => "_", "replace_other" => "_");
				$code = strtoupper(Cutil::translit($param_name, "ru", $transParams));
				//√. .выбираем параметры						
				if(!in_array($code,$params))								
					$params[$code] = $param_name;
		}
	}


if (!empty($arSetupErrors))
	echo ShowError(implode('<br />', $arSetupErrors));
?>



<? // начинаетс€ форма ?>
<form method="POST" action="<? echo $APPLICATION->GetCurPage(); ?>" ENCTYPE="multipart/form-data" name="dataload">
<?
if($improvproperty != 1)
$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("CAT_ADM_CSV_IMP_TAB1"), "ICON" => "store", "TITLE" => GetMessage("CAT_ADM_CSV_IMP_TAB1_TITLE")),
	array("DIV" => "edit2", "TAB" => GetMessage("CAT_ADM_CML1_IMP_TAB2"), "ICON" => "store", "TITLE" => GetMessage("CAT_ADM_CML1_IMP_TAB2_TITLE"))
// 	
);
else //если расширенные параметры
$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("CAT_ADM_CSV_IMP_TAB1"), "ICON" => "store", "TITLE" => GetMessage("CAT_ADM_CSV_IMP_TAB1_TITLE")),
	array("DIV" => "edit3", "TAB" => GetMessage("CAT_ADM_CML1_IMP_TAB3"), "ICON" => "store", "TITLE" => GetMessage("CAT_ADM_CML1_IMP_TAB3_TITLE")),
	array("DIV" => "edit2", "TAB" => GetMessage("CAT_ADM_CML1_IMP_TAB2"), "ICON" => "store", "TITLE" => GetMessage("CAT_ADM_CML1_IMP_TAB2_TITLE"))
	// 	
);
$tabControl = new CAdminTabControl("tabControl", $aTabs, false, true);
$tabControl->Begin();

$tabControl->BeginNextTab();

if ($STEP == 1)
{
	?>
	<tr class="heading">
		<td colspan="2" align="center">
			<? echo GetMessage("CAT_FILE_INFO"); ?>
		</td>
	</tr>
	<tr>
		<td valign="top" width="40%"><? echo GetMessage("CATI_DATA_FILE_SITE"); ?>:</td>
		<td valign="top" width="60%">
			<input type="text" name="URL_DATA_FILE" size="40" value="<? echo htmlspecialcharsbx($URL_DATA_FILE); ?>">
			<input type="button" value="<? echo GetMessage("CATI_BUTTON_CHOOSE")?>" onclick="cmlBtnSelectClick();"><?
				CAdminFileDialog::ShowScript(
					array(
						"event" => "cmlBtnSelectClick",
						"arResultDest" => array("FORM_NAME" => "dataload", "FORM_ELEMENT_NAME" => "URL_DATA_FILE"),
						"arPath" => array("PATH" => "/upload/catalog", "SITE" => SITE_ID),
						"select" => 'F',// F - file only, D - folder only, DF - files & dirs
						"operation" => 'O',// O - open, S - save
						"showUploadTab" => true,
						"showAddToMenuTab" => false,
						"fileFilter" => 'xml',
						"allowAllFiles" => true,
						"SaveConfig" => true
						)
				);
		?></td>
	</tr>
	
	<tr>
		<td valign="top" width="40%"><? echo GetMessage("CATI_DATA_FILE_SITE2"); ?>:</td>
		<td valign="top" width="60%">
			<input type="text" name="URL_DATA_FILE2" size="40" value="<? echo htmlspecialcharsbx($URL_DATA_FILE2); ?>">
		</td>
	</tr>
	
	<tr class="heading">
		<td colspan="2" align="center">
			<? echo GetMessage("CAT_IMPORT_IBSET"); ?>
		</td>
	</tr>
	
	<tr>
		<td valign="top" width="40%"><? echo GetMessage("CATI_INFOBLOCK"); ?>:</td>
		<td valign="top" width="60%"><?
			if (!isset($IBLOCK_ID))
				$IBLOCK_ID = 0;
			echo GetIBlockDropDownListEx($IBLOCK_ID, 'IBLOCK_TYPE_ID', 'IBLOCK_ID',array('CHECK_PERMISSIONS' => 'Y','MIN_PERMISSION' => 'W'));
		?></td>
	</tr>
	

	<tr class="heading">
	<td colspan="2" align="center">
		<? echo GetMessage("CAT_IMPORT_SET"); ?>
	</td>
	</tr>
	<tr>
		<td valign="top" width="40%"><label for="IMPORT_CATEGORY"><? echo GetMessage("CAT_IMPORT"); ?></label>:</td>
		<td valign="top" width="60%">
			<input type="hidden" name="IMPORT_CATEGORY" id="IMPORT_CATEGORY_N" value="N">
			<input type="checkbox" name="IMPORT_CATEGORY" id="IMPORT_CATEGORY_Y" value="Y" <? echo (isset($IMPORT_CATEGORY) && 'Y' == $IMPORT_CATEGORY ? "checked": ""); ?>>
		</td>
	</tr>
	
	<tr>
		<td valign="top" width="40%"><label for="IMPORT_CATEGORY"><? echo GetMessage("CAT_IMPORT_SECTION"); ?></label>:</td>
		<td valign="top" width="60%">
			<input type="hidden" name="IMPORT_CATEGORY_SECTION" id="IMPORT_CATEGORY_SECTION_N" value="N">
			<input type="checkbox" name="IMPORT_CATEGORY_SECTION" id="IMPORT_CATEGORY_SECTION_Y" value="Y" <? echo (isset($IMPORT_CATEGORY_SECTION) && 'Y' == $IMPORT_CATEGORY_SECTION ? "checked": ""); ?>>
		</td>
	</tr>
	
	<tr>
		<td valign="top" width="40%"><label for="IMPORT_CATEGORY"><? echo GetMessage("CAT_ID_SECTION"); ?></label>:</td>
		<td valign="top" width="60%">
				<input type="text" name="ID_SECTION" id="ID_SECTION_FOR_I" value="<? echo intval($ID_SECTION); ?>" size="5" >
		</td>
	</tr>
	
	<tr class="heading">
		<td colspan="2" align="center">
			<? echo GetMessage("IMPROVED_LOADING"); ?>
		</td>
	</tr>
	
	<tr>
		<td valign="top" width="40%"><? echo GetMessage("LOAD_PROPERTIES"); ?>: </td>
		<td valign="top" width="60%">
			<input type="checkbox" name="improvproperty" <?php if($improvproperty == 1)echo("checked");?> value="1" />
		</td>
	</tr>



	<tr class="heading">
		<td colspan="2" align="center">
			<? echo GetMessage("CAT_FILTER"); ?>
		</td>
	</tr>
	
	<tr>
		<td valign="top" width="40%"><label for="IMPORT_CATEGORY"><? echo GetMessage("CAT_FILTER"); ?></label>:</td>
		<td valign="top" width="60%">
				<input type="text" name="CAT_FILTER_I" id="ID_SECTION_FOR_I" value="<? echo $CAT_FILTER_I; ?>" size="50" >
		</td>
	</tr>
	

	
	<tr class="heading">
		<td colspan="2" align="center">
			<? echo GetMessage("CAT_OTHER_OPTIONS"); ?>
		</td>
	</tr>
	

	<tr>
		<td valign="top" width="40%"><label for="ONLY_PRICE"><? echo GetMessage("CAT_ONLY_PRICE"); ?> </label>:</td>
		<td valign="top" width="60%">
			<input type="hidden" name="ONLY_PRICE" id="ONLY_PRICE_N" value="N">
			<input type="checkbox" name="ONLY_PRICE" id="ONLY_PRICE_Y" value="Y" <? echo (isset($ONLY_PRICE) && 'Y' == $ONLY_PRICE ? "checked": ""); ?> >
		</td>
	</tr>

	<tr>
		<td valign="top" width="40%"><? echo GetMessage("CATI_AUTO_STEP_TIME");?>:</td>
		<td valign="top" width="60%">
			<input type="text" name="max_execution_time" size="40" value="<? echo intval($max_execution_time); ?>" ><br>
			<small><?echo GetMessage("CATI_AUTO_STEP_TIME_NOTE");?></small>
		</td>
	</tr>
	
		<tr class="heading">
		<td colspan="2" align="center">
			<? echo GetMessage("CAT_PRICE_OPTIONS"); ?>
		</td>
	</tr>
		<tr>
		<td valign="top" width="40%"><? echo GetMessage("CAT_PRICE_MODIFIER");?>:</td>
		<td valign="top" width="60%">
			<input type="text" name="price_modifier" size="40" value="<? echo doubleval($price_modifier); ?>" ><br>
			<small><?echo GetMessage("CAT_PRICE_MODIFIER_INFO");?></small>
		</td>
	</tr>

    <? if ($ACTION != "IMPORT")
    {
        ?>
	<tr>
		<td valign="top" width="40%" ><?echo GetMessage("CAT_PROFILE_NAME");?>:</td>
		<td valign="top" width="60%" >
		 <input type="text" name="SETUP_PROFILE_NAME" size="40" value="<? echo htmlspecialcharsbx($SETUP_PROFILE_NAME); ?>">

				<br><br>
		</td>

    </tr>
      <? } ?>

<?
}

$tabControl->EndTab();
$tabControl->BeginNextTab();
if ($STEP == 2 && $improvproperty == 1)  //если выбор свойств
{
	//прив€зка параметров
?>
	<tr class="heading">
		<td colspan="2"><?echo GetMessage("IBLOCK_ADM_IMP_FIELDS_SOOT"); ?></td>
	</tr>
	<?
	$arAvailFields = array();
	foreach ($params as $code => $name)
	{
		$arReadFields[] = array(
			"value" => $code,
			"name" => $name,
		);
	}

	$properties = CIBlockProperty::GetList(array(
		"sort" => "asc",
		"name" => "asc",
	) , array(
		"ACTIVE" => "Y",
		"IBLOCK_ID" => $IBLOCK_ID,
		"CHECK_PERMISSIONS" => "N",
	));
	while ($prop_fields = $properties->Fetch())
	{
		
		$arAvailFields[] = array(
			"value" => $prop_fields["ID"],
			"name" => GetMessage("IBLOCK_ADM_IMP_FI_PROPS")." \"".$prop_fields["NAME"]."\"",
			"code" => $prop_fields["CODE"],
		    "type" => $prop_fields["PROPERTY_TYPE"]
		);
	}

	

//$CONNECTIONS
?>
	<tr>
			<td><?echo GetMessage("PARAMETERS_FROM_YML"); ?></td>
			<td><?echo GetMessage("PARAMETERS_FROM_IB"); ?></td>			
			</tr>
<?php
	$cnt = 0; 
	foreach ($arReadFields as $field)
	{?>
		<tr>
			<td width="40%">
				<b><?echo htmlspecialcharsbx($field["name"]); ?></b>:
			</td>
			<td width="60%">
			<table>
		
				<tr>
					<td>
					<select onchange="javascript:if(this.value != '') document.getElementById('id<?echo $field["value"] ?>').style.display='none'; else document.getElementById('id<?echo $field["value"] ?>').style.display='block';" name="CONNECTIONS[]">
					<?/*<option value=""><?echo GetMessage("PARAMETERS_CREATE_NEW"); ?></option>*/?>
					<?
					$cn = $cnt*3;
					$was = false;
						foreach ($arAvailFields as $k => $ar)
						{
							
				?>
										<option <?=$CONNECTIONS[$cn]?> value="<?echo $ar["value"] ?>" <?if(is_array($CONNECTIONS) && $CONNECTIONS[$cn] == $ar["value"]){?>selected<? $was = true;}?>><?echo htmlspecialcharsbx($ar["name"]); ?></option>
										<?
						}
				?>
				</select>
					</td>
					<td><div id="id<?echo $field["value"] ?>" <?if($was){?>style="display:none;"<?}?>>
						<select name="CONNECTIONS[]">
						<option value="S" <?
					if ($bSelected)
						echo "selected" ?>><?echo GetMessage("PARAMETERS_FROM_IB_STRING"); ?></option>
						<option value="L" <?
					if ($bSelected)
						echo "selected" ?>><?echo GetMessage("PARAMETERS_FROM_IB_LIST"); ?></option>
					</select>
					<input type="hidden" name="CONNECTIONS[]"  value="<?=$field["value"];?>">
					</div>
					</td>
				</tr>
			</table>
				
				
			</td>
		</tr>
		<?
		$cnt++;
	}
	?>
	<tr><td colspan="2"><hr /></tr>

	
<?
}

$tabControl->EndTab();
?>

<?
if (($STEP == 2 && $improvproperty != 1) || $STEP == 3)  //упрощенна€ выгрузка
{
    $tabControl->BeginNextTab();

	$FINITE = true;

	$tabControl->EndTab();
}
?>

<?php 

$tabControl->Buttons();

echo bitrix_sessid_post();


if ($ACTION == 'IMPORT_EDIT' || $ACTION == 'IMPORT_COPY')
{
    ?><input type="hidden" name="PROFILE_ID" value="<? echo intval($PROFILE_ID); ?>"><?
}
?>
	<input type="hidden" name="STEP" value="<?echo intval($STEP) + 1;?>">
<?php 
if ($STEP == 2 && $improvproperty == 1)  //если выбор свойств
{

	?> 

    <input type="hidden" name="lang" value="<?echo LANGUAGE_ID; ?>">
    <input type="hidden" name="ACT_FILE" value="<?echo htmlspecialcharsbx($_REQUEST["ACT_FILE"]) ?>">
    <input type="hidden" name="ACTION" value="<?echo htmlspecialcharsbx($ACTION) ?>">
    <input type="hidden" name="IBLOCK_ID" value="<?echo $_REQUEST["IBLOCK_ID"] ?>">
    <input type="hidden" name="DATA_FILE_NAME"  value="<?echo $DATA_FILE_NAME ?>">
    <input type="hidden" name="improvproperty"  value="<?echo intval($improvproperty);?>">
	
	
	<input type="hidden" name="max_execution_time" value="<?echo htmlspecialcharsbx($_REQUEST["max_execution_time"]) ?>">

	<input type="hidden" name="IMPORT_CATEGORY" value="<?echo htmlspecialcharsbx($_REQUEST["IMPORT_CATEGORY"]) ?>">
	<input type="hidden" name="ONLY_PRICE" value="<?echo htmlspecialcharsbx($_REQUEST["ONLY_PRICE"]) ?>">
	<input type="hidden" name="IMPORT_CATEGORY_SECTION" value="<?echo htmlspecialcharsbx($_REQUEST["IMPORT_CATEGORY_SECTION"]) ?>">
	<input type="hidden" name="URL_DATA_FILE2" value="<?echo htmlspecialcharsbx($_REQUEST["URL_DATA_FILE2"]) ?>">
	<input type="hidden" name="ID_SECTION" value="<?echo htmlspecialcharsbx($_REQUEST["ID_SECTION"]) ?>">
	<input type="hidden" name="CAT_FILTER_I" value="<?echo htmlspecialcharsbx($_REQUEST["CAT_FILTER_I"]) ?>">
	<input type="hidden" name="price_modifier" value="<?echo htmlspecialcharsbx($_REQUEST["price_modifier"]) ?>">
	<input type="hidden" name="SETUP_PROFILE_NAME" value="<?echo htmlspecialcharsbx($_REQUEST["SETUP_PROFILE_NAME"]) ?>">
	   
	   
    <input type="hidden" name="SETUP_FIELDS_LIST" value="improvproperty, CONNECTIONS, DATA_FILE_NAME, IBLOCK_ID, IMPORT_CATEGORY, ONLY_PRICE, max_execution_time, IMPORT_CATEGORY_SECTION, URL_DATA_FILE2, ID_SECTION, CAT_FILTER_I, price_modifier">

	<input type="submit" value="<? echo GetMessage("CET_PREV_STEP"); ?>" name="prev_btn">
    <input type="submit" value="<? echo ($ACTION=="IMPORT")?GetMessage("CICML_NEXT_STEP_F")." &gt;&gt;":GetMessage("CET_SAVE"); ?>" name="submit_btn"><?
}

if ($STEP == 1)
{

	?> 

    <input type="hidden" name="lang" value="<?echo LANGUAGE_ID; ?>">
    <input type="hidden" name="ACT_FILE" value="<?echo htmlspecialcharsbx($_REQUEST["ACT_FILE"]) ?>">
    <input type="hidden" name="ACTION" value="<?echo htmlspecialcharsbx($ACTION) ?>">
    <?if($improvproperty){?>
    <input type="hidden" name="CONNECTIONS" value='<?=serialize($CONNECTIONS)?>'>
    <?}?>
    <input type="hidden" name="SETUP_FIELDS_LIST" value="improvproperty, CONNECTIONS, DATA_FILE_NAME, IBLOCK_ID, IMPORT_CATEGORY, ONLY_PRICE, max_execution_time, IMPORT_CATEGORY_SECTION, URL_DATA_FILE2, ID_SECTION, CAT_FILTER_I, price_modifier">
    <input type="submit" value="<? echo ($ACTION=="IMPORT")?GetMessage("CICML_NEXT_STEP_F")." &gt;&gt;":GetMessage("CET_NEXT_STEP"); ?>" name="submit_btn"><?
}

$tabControl->End();

?></form>

<script type="text/javascript">
<?if ($STEP == 1):?>
tabControl.SelectTab("edit1");
tabControl.DisableTab("edit2");
tabControl.DisableTab("edit3");
<?endif;?>
<?if ($STEP == 2):?>
tabControl.DisableTab("edit1");
tabControl.DisableTab("edit2");
tabControl.SelectTab("edit3");
<?endif;?>
<?if ($STEP == 3):?>
tabControl.DisableTab("edit1");
tabControl.SelectTab("edit2");
tabControl.DisableTab("edit3");
<?endif;?>
</script>