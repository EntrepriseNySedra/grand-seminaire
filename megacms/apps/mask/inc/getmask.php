<?php
if (!defined('MEGA_ROOT')) define("MEGA_ROOT", realpath(__DIR__."/../../../../"));
//packages
require_once(MEGA_ROOT."/megacms/core/inc/db.php");
require_once(MEGA_ROOT."/megacms/core/inc/settings.php");
require_once(MEGA_ROOT."/megacms/core/inc/security.php");

MEGA_session_lite();

try{
    if(MEGA_login_verify() !== true){
        throw new Exception("Du bist nicht angemeldet. Bitte lade diese Seite neu.");
    }

    //JSON check
    $decoded = MEGA_json_check();
    if($decoded == false){
      throw new Exception($MEGA_LANG['ERR_INP_JSON']);
    }

    $masks = MEGA_get_masks($decoded["content"]);

  $result_return["error"] = array(
    "status" => true,
    "message" => ""
  );
    $result_return["masks"] = $masks;
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
