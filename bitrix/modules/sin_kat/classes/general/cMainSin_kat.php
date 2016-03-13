<?php
class cMainSin_kat {
    static $MODULE_ID="Sin_kat";

    /**
     * Хэндлер, отслеживающий изменения в инфоблоках
     * @param $arFields
     * @return bool
     */
    static function onBeforeElementUpdateHandler($arFields){
        // чтение параметров модуля
        // $iblock_id = COption::GetOptionString(self::$MODULE_ID, "iblock_id");

        // Активная деятельность

        // Результат
        return true;
    }
    
    /**
     * Добавление свойства для связывания инфоблоков
     * @param type $name
     * @param type $i_blok
     * @param type $codep
     */
    function addProp($name="ID_постащика",$i_blok=10,$codep="id_post") {
      $arFields = Array(
        "NAME" => $name,
        "ACTIVE" => "Y",
        "SORT" => "5",
        "CODE" =>$codep ,
        "PROPERTY_TYPE" => "S",
        "IBLOCK_ID" => $i_blok,
        );
      
      $ibp = new CIBlockProperty;
      $PropID = $ibp->Add($arFields);
      return $PropID;
    }
    
    /**
     * Деинсталяция свойства для связывания инфоблоков
     * @param type $codep
     * @param type $i_blok
     */
    function delProp($codep="id_post",$i_blok=10) {
       $res = CIBlockProperty::GetByID($codep, $i_blok, FALSE);
        if($ar_res = $res->GetNext()){
        CIBlockProperty::Delete($ar_res['ID']);
        }
    }
}