<?php
//include packages
if (!defined('MEGA_ROOT')) define("MEGA_ROOT", realpath(__DIR__."/../../../../"));
require_once(MEGA_ROOT.'/megacms/core/inc/db.php');
require_once(MEGA_ROOT.'/megacms/core/inc/simple_html_dom.php');
require_once(MEGA_ROOT.'/megacms/core/inc/settings.php');
require_once(MEGA_ROOT.'/megacms/core/inc/security.php');
MEGA_session();
try{
    //session check
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

    //check input
    $url = $decoded['url'];
    $url = MEGA_repair_url($url);
    if($url == false){
      throw new Exception($MEGA_LANG['ERR_INP_URL']);
    }

    $url = parse_url($url)['path'];
    $url = preg_replace("/_cms_temp\.php$/","",$url);

    $type = $decoded['type'];

    if($type !== "cancel"){
        //task check
        if(megacms_task_check($url,'loggedin')==false){
            throw new Exception($MEGA_LANG['ERR_AUTH']);
        }

        //get code from db
        $filecontent = megacms_task_code($url);

        //write in history
        if(megacms_history($url,$filecontent)==false){
            throw new Exception($MEGA_LANG['ERR_404_DB']);
        }

        //remove cms-tags
        $filecontent = str_replace(array("\r\n", chr(10).chr(13), "\r", "\n", PHP_EOL, chr(10), chr(13)),'--jo:r--', $filecontent);
        $code = str_get_html($filecontent);
        foreach ($code->find('* [data-jo="true"]') as $key) {
          $key->removeAttribute ( "data-jo");
        }

        //set cms-tags
        $positions = $decoded['sec'];
        foreach ($positions as $key) {
          if(filter_var($key,FILTER_VALIDATE_INT) == false AND $key != 0){
            throw new Exception($MEGA_LANG['ERR_INP']);
          }
          $elem = $code->find('*[data-cmsid="'.$key.'"]');
          if($elem[0] == false){
            throw new Exception($MEGA_LANG['ERR_INP']);
          }
          $elem[0]->setAttribute("data-jo","true");
        }

        foreach ($code->find('* [data-cmsid]') as $key) {
          $key->removeAttribute ("data-cmsid");
        }
        $filecontent = str_replace("--jo:r--", PHP_EOL,  $code->save());

        //write in file
        $myfile = fopen(MEGA_ROOT.$url, "w+");
        if(!$myfile){
            throw new Exception($MEGA_LANG['ERR_404_FILE']);
        }

        fwrite($myfile,$filecontent);
        fclose($myfile);

        //delete task and temp file
        megacms_task_delete($url);
    }else{
        //delete task and temp file if no error
        if(megacms_task_check($url,'loggedin')==false){
        }else{
            megacms_task_delete($url);
        }
    }






    $result_return["error"] = array(
      "status" => true,
      "message" => ""
    );
    $result_return["redirection"] = $url;
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
