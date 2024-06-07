<?php
if (!defined('MEGA_ROOT')) define("MEGA_ROOT", realpath(__DIR__."/../../../"));
require_once(MEGA_ROOT."/megacms/core/inc/security.php");

//reads settings from settings.ini
//input: -
//return: settings (array)
function MEGA_get_settings(){
  $settings = file_get_contents(MEGA_ROOT."/megacms/core/settings/settings.ini");
  $settings = parse_ini_string($settings,true);

  return $settings;
}

//rewrites settings.ini with new values if defined
//input: new settings (array)
// return: true if success
function MEGA_set_settings($settings){
  $old = MEGA_get_settings();
  $lang = $old['lang'];
  $string = "";
  foreach ($old as $key => $value) {
    $char = "";
    if(array_key_exists($key,$settings)){
      $value = $settings[$key];
      $old[$key] = $value;
    }
    if(!is_numeric($value) AND !is_bool($value)){
      $char = "\"";
    }
    $line = "\r\n".$key." = ".$char.htmlspecialchars($value).$char;
    $string = $string.$line;
  }
  if(file_put_contents(MEGA_ROOT."/megacms/core/settings/settings.ini",$string)== false){
    return false;
  }
  $GLOBALS['MEGA_S'] = $old;
  if($GLOBALS['MEGA_S']['lang'] != $lang){
    $GLOBALS['MEGA_LANG'] = MEGA_language($GLOBALS['MEGA_S']['lang']);
  }
  return true;
}

//gets language contents or default en
//input: language abbreviation, [path to lang files]
//return: language (array)
function MEGA_language($lang,$path = "/megacms/core/lang/"){
  $url = $path.$lang.".json";
  if(MEGA_repair_url($url,array("json"))==false){
    $url = "/megacms/core/lang/en.json";
  }
  $lang_set = json_decode(file_get_contents(MEGA_ROOT.$url), true);
  $lang_en_set = json_decode(file_get_contents(MEGA_ROOT.$path."en.json"), true);
  $lang_set = array_merge($lang_en_set, $lang_set);
  return $lang_set;
}
function MEGA_language_available(){
  return glob(MEGA_ROOT."/megacms/core/lang/*.json");
}
$MEGA_S = MEGA_get_settings();
$MEGA_LANG = MEGA_language($MEGA_S['lang']);
?>
