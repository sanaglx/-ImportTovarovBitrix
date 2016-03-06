<?php IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/adlon.import/options.php'); 
use Bitrix\Main\Localization\Loc;
$module_id = 'adlon.import';


    if ($_SERVER['REQUEST_METHOD'] == 'POST' && check_bitrix_sessid())
	{
		if(isset($_POST['Update']) && $_POST['Update'] === 'Y' && is_array($_POST['SETTINGS']))
		{
			foreach($_POST['SETTINGS'] as $k=>$v)
			{
				COption::SetOptionString($module_id, $k, $v);
			}

			LocalRedirect($APPLICATION->GetCurPage().'?lang='.LANGUAGE_ID.'&mid=adlon.import&mid_menu=1');
                        
		}
	}

 ?>       

<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?lang=<?echo LANGUAGE_ID?>&mid=<?=$module_id?>" ENCTYPE="multipart/form-data" name="dataload">
    <?php
    $aTabs = array(
        array("DIV" => "edit1", "TAB" => GetMessage("B_YML_TAB_NAME"), "ICON" => "store", "TITLE" => GetMessage("CAT_ADM_CSV_IMP_TAB3_TITLE"))
    );
    
    $tabControl = new CAdminTabControl("tabControl", $aTabs, false, true);
    $tabControl->Begin();

    $tabControl->BeginNextTab();
    {
        ?>
        <tr>
            <td colspan="2" valign="top" width="40%"><? echo GetMessage("B_YML_TAB_TITLE"); ?>
            </td>
        </tr>
        <tr><td colspan="2">ПАРАМЕТРЫ</td> </tr>
 
        <tr>
		<td width="50%">Каталог товыров (ID)</td>
		<td width="50%">
			<input type="text" name="SETTINGS[idIblokCatalog]" value="10">
		</td>
	</tr>
        <tr>
		<td width="50%">Торговые предложения (ID)</td>
		<td width="50%">
			<input type="text" name="SETTINGS[idIblokPredlog]" value="11">
		</td>
	</tr>
        <tr>
		<td width="50%">Импорт Каталог (ID)</td>
		<td width="50%">
			<input type="text" name="SETTINGS[idIblokCatalogImp]" value="22">
		</td>
	</tr>
                <tr>
		<td width="50%">Импорт Торговые предложения (ID)</td>
		<td width="50%">
			<input type="text" name="SETTINGS[idIblokPredlogImp]" value="23">
		</td>
	</tr>
                <tr>
		<td width="50%">Свойтво по которому связываем каталоги(ID) </td>
		<td width="50%">
			<input type="text" name="SETTINGS[nomPropertyCatalog]" value="464">
		</td>
	</tr>
                <tr>
		<td width="50%">Свойтво по которому связываем торговые предложения (ID)</td>
		<td width="50%">
			<input type="text" name="SETTINGS[nomPropertyPredlog]" value="543">
		</td>
	</tr>
        <?php
	//$tabControl->Buttons();
        ?>
        
        </tr>
                <tr>
		<td width="50%"></td>
		<td width="50%">
			<input type="submit" name="Update" value="Записать">
		</td>
	</tr>
        
	<input type="hidden" name="Update" value="Y">
        <?php
    }

    $tabControl->EndTab();


    $tabControl->Buttons();

    echo bitrix_sessid_post(); {
    ?>
        
        <input type="submit" value="OK" name="submit_btn">
        
    <?php
    }



    $tabControl->End();
    ?></form>
<script type="text/javascript">
    tabControl.SelectTab("edit1");
</script>