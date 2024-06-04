<?php
if (!defined('MEGA_ROOT')) define("MEGA_ROOT", realpath(__DIR__."/../../../"));
require_once(MEGA_ROOT.'/megacms/core/inc/db.php');
require_once(MEGA_ROOT.'/megacms/core/inc/security.php');
require_once(MEGA_ROOT.'/megacms/core/inc/settings.php');
error_reporting(0);
MEGA_session_lite();
try{

    if(MEGA_login_verify() !== true){
        throw new Exception($MEGA_LANG['ERR_AUTH']);
    }

    //JSON check
    $decoded = MEGA_json_check();
    if($decoded == false){
      throw new Exception($MEGA_LANG['ERR_INP_JSON']);
    }

    $url = $decoded['url'];
    //corrects url
    $url = MEGA_repair_url($url);
    $url = preg_replace("/_cms_temp\.php$/","",$url);
    if($url == false){
      throw new Exception($MEGA_LANG['ERR_INP_URL']);
    }
    if(megacms_task_check($url,'loggedin')==false){
        throw new Exception($MEGA_LANG['ERR_AUTH']);
    }
    megacms_task_valid($url);

    $result_return["error"] = array(
      "status" => true,
      "message" => ""
    );
    header('Content-type: application/json');
    echo json_encode($result_return);
}
catch(Exception $e){
  $result_return["error"] = array(
    "status" => false,
    "message" => $e->getMessage()
  );
  header('Content-type: application/json');
  echo json_encode($result_return);
}




?>
