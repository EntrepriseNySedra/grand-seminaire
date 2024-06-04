<?php
if (!defined('MEGA_ROOT')) define("MEGA_ROOT", realpath(__DIR__."/../../../../"));
require_once(MEGA_ROOT.'/megacms/core/inc/db.php');
require_once(MEGA_ROOT.'/megacms/core/inc/security.php');
require_once(MEGA_ROOT.'/megacms/core/inc/settings.php');
error_reporting(0);
MEGA_session();
try{

    if(MEGA_login_verify() !== true){
        throw new Exception($MEGA_LANG['ERR_AUTH']);
    }
    if($_SESSION['megacms_user'] != 'admin'){
      throw new Exception($MEGA_LANG['ERR_AUTH_ADM']);
    }

    //JSON check
    $decoded = MEGA_json_check();
    if($decoded == false){
      throw new Exception($MEGA_LANG['ERR_INP_JSON']);
    }

    $mask = MEGA_get_masks($decoded["id"])[0];
    $result_return["id"] = $mask["id"];
    $result_return["name"] = $mask["name"];
    $result_return["code"] = $mask["code"];

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
