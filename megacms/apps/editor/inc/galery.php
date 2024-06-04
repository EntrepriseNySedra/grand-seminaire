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
    if(strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') != 0){
        throw new Exception("Request Method Error");
    }

    $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
    if(strpos(strtolower($contentType), 'application/json') != 0){
        throw new Exception($MEGA_LANG['ERR_INP_JSON']);
    }

    $log_directory = MEGA_ROOT.$GLOBALS['MEGA_S']['up_dir'];
    if(!is_readable($log_directory)){
        throw new Exception($MEGA_LANG['ERR_404_DIR']);
    }
    $dirname = MEGA_ROOT.$GLOBALS['MEGA_S']['up_dir'];
    $files = [];
    if($handle = opendir($dirname)) {
	   while(false !== ($file = readdir($handle)))
				    if(is_dir($dirname."/".$file)){

				    }
				    else {
					   if ($file != "." && $file != ".." && !strstr($file, "_thumb.jpg") && !strstr($file, "_thumb.png") && !strstr($file, "_thumb.gif") && (strstr($file, ".jpg")  OR strstr($file, ".png" ) OR strstr($file, ".gif"))){
					       $files[] = $GLOBALS['MEGA_S']['up_dir'].$file;
				        }

                    }
			         closedir($handle);
		        }

  $result_return["error"] = array(
    "status" => true,
    "message" => ""
  );
    $result_return["files"] = $files;
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
