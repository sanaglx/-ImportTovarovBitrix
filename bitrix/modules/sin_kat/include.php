<?php
CModule::IncludeModule("dull");
global $DBType;

$arClasses=array(
    'cMainDull'=>'classes/general/cMainSin_kat.php'
);

CModule::AddAutoloadClasses("Sin_kat",$arClasses);
