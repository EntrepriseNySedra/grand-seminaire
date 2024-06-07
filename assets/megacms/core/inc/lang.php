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

    $result_return['lang'] = $MEGA_LANG;
    $result_return['set'] = $MEGA_S;
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
