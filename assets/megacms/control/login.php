<?php
if (!defined('MEGA_ROOT')) define("MEGA_ROOT", realpath(__DIR__."/../../"));
require_once(MEGA_ROOT.'/megacms/core/inc/db.php');
require_once(MEGA_ROOT.'/megacms/core/inc/security.php');
require_once(MEGA_ROOT.'/megacms/core/inc/settings.php');
require_once(MEGA_ROOT.'/megacms/control/fileexplorer.php');

try{
  $session = MEGA_session();
  if ($session == false){
    throw new Exception($MEGA_LANG['ERR_AUTH_SEC']);
  }

  //delete old tasks
  megacms_taskkiller();

  if(!isset($_SESSION['megacms_active'])){
    $_SESSION['megacms_active'] = false;
  }
  if(MEGA_login_verify() != true){

      if(!isset($_POST['name']) OR !isset($_POST['password'])){
          throw new Exception($MEGA_LANG['LGN_ERR']);
      }else{
          $password = $_POST['password'];
          $name = $_POST['name'];
          if(filter_var($name,FILTER_VALIDATE_EMAIL) != true){
            throw new Exception($MEGA_LANG['LGN_ERR']);
          }
          $output = megacms_login_check($name,$password);
          if($output == false){
              throw new Exception($MEGA_LANG['LGN_ERR']);
          }else{
              $_SESSION['megacms_active'] = true;
              $_SESSION['megacms_id'] = $output[0];
              $_SESSION['megacms_mail'] = $output[1];
              $_SESSION['megacms_user'] = $output[2];
              $_SESSION['megacms_agent'] = $_SERVER['HTTP_USER_AGENT'];
              $_SESSION['megacms_files'] = MEGA_read_dir(MEGA_ROOT);
          }
      }

  }

  header('Location: /megacms/control/cms.php');
}
catch(Exception $e){
  if(isset($_POST["times"])){
      $message = '<div style="color: red;">'.htmlentities($e->getMessage()).'</div>';
      $mail = htmlentities($_POST['name']);
  }else{
      $message = '';
      $mail = "";
  }
}
