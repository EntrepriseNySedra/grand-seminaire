<?php
if (!defined('MEGA_ROOT')) define("MEGA_ROOT", realpath(__DIR__."/../../"));
//packages
require_once(MEGA_ROOT."/megacms/core/inc/settings.php");
require_once(MEGA_ROOT."/megacms/core/inc/security.php");

MEGA_session();
if(MEGA_login_verify() != true){
    header('Location: /megacms');
    exit;
}
$error = $MEGA_LANG['SET_OK'];
try{
  if($_SESSION['megacms_user'] != 'admin'){
    header('Location: /megacms');
    exit;
  }
  if(isset($_POST['type'])){
    switch ($_POST['type']) {
      case 'users':
        if(isset($_POST['save'])){
          foreach ($_POST as $key => $item) {
            if(gettype($key) == "integer"){
              $type = isset($item['type']) ? $item['type'] : NULL;
              MEGA_update_users($key, $type);
            }elseif($key == "new"){
              if($item['mail'] != "" AND $item['type'] != ""){
                if(sizeof(MEGA_get_users($item['mail'], "mail")) >0){
                  throw new Exception($MEGA_LANG['SET_ERR_MAIM']);
                }
                if(MEGA_set_user($item['mail'], $item['type'], $MEGA_S['std_pw']) == false){
                  throw new Exception($MEGA_LANG['ERR_INP']."1");

                }
              }
            }
          }
        }elseif(isset($_POST['delete'])){
          foreach ($_POST['delete'] as $key => $value) {
            if($key == $_SESSION['megacms_id']){
              throw new Exception($MEGA_LANG['ERR_INP']);
            }
            MEGA_delete_user($key);
          }
        }else{
          throw new Exception($MEGA_LANG['ERR_INP']);
        }
        break;
      case 'change_stdpw':
        if(!isset($_POST['std_pw'])){
          throw new Exception($MEGA_LANG['SET_ERR']);
        }
        $set['std_pw'] = $_POST['std_pw'];
        if(!MEGA_set_settings($_POST)){
          throw new Exception($MEGA_LANG['SET_ERR']);
        }
        break;
      default:
        break;
    }
  }else{
    $error = "";
  }
}
catch(Exception $e){
  $error = $e->getMessage();
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $MEGA_LANG['SET']; ?> | megacms</title>
    <script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
    <script type="text/javascript" src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="/megacms/core/js/ui.js"></script>
    <script>
      $(document).ready(function(){
        MEGA_folder_toggle($('.MEGA_folder'));
      });
    </script>
    <link href="/megacms/core/style/css/stylesheet.css" rel="stylesheet" type="text/css"/>
    <link href="/megacms/control/css/backend_style.css" rel="stylesheet" type="text/css"/>
    <link rel="apple-touch-icon" sizes="57x57" href="/megacms/core/style/icons/favicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/megacms/core/style/icons/favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/megacms/core/style/icons/favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/megacms/core/style/icons/favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/megacms/core/style/icons/favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/megacms/core/style/icons/favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/megacms/core/style/icons/favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/megacms/core/style/icons/favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/megacms/core/style/icons/favicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="/megacms/core/style/icons/favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/megacms/core/style/icons/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/megacms/core/style/icons/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/megacms/core/style/icons/favicon/favicon-16x16.png">
    <link rel="manifest" href="/megacms/core/style/icons/favicon/manifest.json">
    <meta name="msapplication-TileColor" content="#BF0040">
    <meta name="msapplication-TileImage" content="/megacms/core/style/icons/favicon/ms-icon-144x144.png">
    <meta name="theme-color" content="#BF0040">

</head>
<body >
    <div id="navigation">
    <div id="nav_container">
      <div id="sidebar">
          <div>
              <a href="/megacms/control/cms.php" title="<?php echo $MEGA_LANG['CMS']; ?>"><img src="/megacms/core/style/icons/home.svg"/></a>

              <?php echo $_SESSION['megacms_user'] == 'admin' ? '<a href="/megacms/control/users.php" id="active" title="'.$MEGA_LANG['USR'].'"><img src="/megacms/core/style/icons/users.svg" /></a>' : ''  ?>
      
              <a href="/megacms/control/logout.php" title="<?php echo $MEGA_LANG['BYE_TITL']; ?>"><img src="/megacms/core/style/icons/logout.svg"/></a>
          </div>
      </div>
    </div>
    </div>
	<div id="menu">
	<div>
    <h1><?php echo $MEGA_LANG['USR']; ?></h1>
    <?php
    if($error != ""){
      echo '
      <div class="settings" style="background-color: #fc462a; color: white;">
        <span>'.$error.'</span>
      </div>
      ';
    }
     ?>

      <div class="settings">
          <a class="MEGA_folder MEGA_folder_active"><?php echo $MEGA_LANG['USR']; ?></a>
          <div>
            <form class="MEGA_form" method="post" action="">
              <fieldset>
                <table>
                  <tbody>
                      <tr>
                        <td>
                          <input name="new[mail]" type="email" placeholder="<?php echo $MEGA_LANG['SET_MAIL_NEW'] ?>" />
                        </td>
                        <td>
                          <select name="new[type]">
                            <option value="standard">
                              <?php echo $MEGA_LANG['USR_STD'] ?>
                            </option>
                            <option value="admin">
                              <?php echo $MEGA_LANG['USR_ADM'] ?>
                            </option>
                          </select>
                        </td>
                        <td>
                        </td>
                      </tr>
                      <?php
                        $users = MEGA_get_users('all', 'type');
                        foreach ($users as $item){
                          $standard = "";
                          $admin = "";
                          $self = false;
                          if($item['id'] == $_SESSION['megacms_id']){
                            $self = true;
                          }
                          if($item['type'] == "admin"){
                            $admin = ' selected="selected"';
                          }else{
                            $standard = ' selected="selected"';
                          }
                          echo '  <tr>
                              <td>
                                '.$item['mail'].'
                              </td>
                              <td>
                                <select '.($self ? 'disabled="true"' : '').' name="'.$item['id'].'[type]">
                                  <option  value="standard"'.$standard.'>
                                    '.$MEGA_LANG['USR_STD'].'
                                  </option>
                                  <option value="admin"'.$admin.'>
                                    '.$MEGA_LANG['USR_ADM'].'
                                  </option>
                                </select>
                              </td>
                              <td>';
                              if(!$self){
                                echo '<input type="submit" class="MEGA_btn_gray" value="'.$MEGA_LANG['FORM_DEL'].'" name="delete['.$item['id'].']"/>';
                              }
                          echo '
                              </td>
                            </tr>';
                        }
                      ?>
                  </tbody>
                </table>
              </fieldset>
              <input type="hidden" name="type" value="users" />
              <input type="submit" value="<?php echo $MEGA_LANG['FORM_OK'] ?>" name="save"/>
            </form>
          </div>
      </div>
      

	</div>
	</div>

</body>
</html>
