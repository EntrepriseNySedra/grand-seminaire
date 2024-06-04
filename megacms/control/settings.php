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
  if(isset($_POST['type'])){
    $type = $_POST['type'];
    switch ($type) {
      case 'change_pw':
        if(!isset($_POST['pw']) OR !isset($_POST['new']) OR !isset($_POST['new2'])){
          throw new Exception($MEGA_LANG['SET_ERR']);
        }
        if(megacms_login_check($_SESSION['megacms_mail'], $_POST['pw']) != true){
          throw new Exception($MEGA_LANG['SET_ERR']);
        }
        if($_POST['new'] != $_POST['new2']){
          throw new Exception($MEGA_LANG['SET_ERR_PW']);
        }
        $pw = $_POST['new'];
        if(MEGA_set_password($_SESSION['megacms_mail'],$pw) == false){
          throw new Exception($MEGA_LANG['SET_ERR_PWM']);
        }
        break;
      case 'change_mail':
        if(!isset($_POST['pw']) OR !isset($_POST['new']) OR !isset($_POST['old'])){
          throw new Exception($MEGA_LANG['SET_ERR']);
        }
        if(filter_var($_POST['new'],FILTER_VALIDATE_EMAIL) != true OR filter_var($_POST['old'],FILTER_VALIDATE_EMAIL) != true){
          throw new Exception($MEGA_LANG['LGN_ERR']);
        }
        if($_SESSION['megacms_mail'] != $_POST['old']){
          throw new Exception($MEGA_LANG['SET_ERR_MAIL']);
        }
        if(megacms_login_check($_SESSION['megacms_mail'], $_POST['pw']) != true){
          throw new Exception($MEGA_LANG['SET_ERR']);
        }
        $mail = $_POST['new'];
        if(MEGA_set_mail($_SESSION['megacms_mail'],$mail) == false){
          throw new Exception($MEGA_LANG['SET_ERR_MAIM']);
        }
        $_SESSION['megacms_mail'] = $mail;
        break;
      case 'change_syst':
        if($_SESSION['megacms_user'] != 'admin'){
          throw new Exception($MEGA_LANG['ERR_AUTH_ADM']);
        }
        if(!isset($_POST['httpsonly'])){
          $_POST['httpsonly']= 0;
        }elseif($_POST['httpsonly'] == true){
          $_POST['httpsonly']= 1;
        }
        if(!isset($_POST['charset']) OR !isset($_POST['lang']) OR !isset($_POST['history']) OR $_POST['history']<0){
          throw new Exception($MEGA_LANG['SET_ERR']);
        }
        if(filter_var($_POST['history'],FILTER_VALIDATE_INT) != true OR (filter_var($_POST['httpsonly'],FILTER_VALIDATE_BOOLEAN) != true AND $_POST['httpsonly'] != false)){
          throw new Exception($MEGA_LANG['SET_ERR']);
        }
        $_POST['lang'] = strtolower($_POST['lang']);
        if(!MEGA_set_settings($_POST)){
          throw new Exception($MEGA_LANG['SET_ERR']);
        }
        break;
      case 'change_upl':
        if($_SESSION['megacms_user'] != 'admin'){
          throw new Exception($MEGA_LANG['ERR_AUTH_ADM']);
        }
        if(!isset($_POST['up_dir']) OR !isset($_POST['img_size']) OR !isset($_POST['img_quality']) OR $_POST['img_quality']<1 OR $_POST['img_quality']>100 OR $_POST['img_size']<1){
          throw new Exception($MEGA_LANG['SET_ERR']);
        }
        if(filter_var($_POST['img_size'],FILTER_VALIDATE_INT) != true OR filter_var($_POST['img_quality'],FILTER_VALIDATE_INT) != true){
          throw new Exception($MEGA_LANG['SET_ERR']);
        }
        if(!MEGA_set_settings($_POST)){
          throw new Exception($MEGA_LANG['SET_ERR']);
        }
        break;
      case 'change_ed':
        if($_SESSION['megacms_user'] != 'admin'){
          throw new Exception($MEGA_LANG['ERR_AUTH_ADM']);
        }
        if(!isset($_POST['ed_block'])){
          throw new Exception($MEGA_LANG['SET_ERR']);
        }
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
                    <a href="/megacms/control/cms.php"  title="<?php echo $MEGA_LANG['CMS']; ?>"><img src="/megacms/core/style/icons/home.svg"/></a>
                    <?php echo $_SESSION['megacms_user'] == 'admin' ? '<a href="/megacms/apps/mask/mask.php"  title="'.$MEGA_LANG['MSK_TITL'].'"><img src="/megacms/core/style/icons/mask.svg"/></a>' : '' ?>
                    <?php echo $_SESSION['megacms_user'] == 'admin' ? '<a href="/megacms/control/users.php" title="'.$MEGA_LANG['USR'].'"><img src="/megacms/core/style/icons/users.svg" /></a>' : ''  ?>
                    <a id="active" href="/megacms/control/settings.php" title="<?php echo $MEGA_LANG['SET']; ?>"><img src="/megacms/core/style/icons/settings.svg"/></a>
                    <a href="/megacms/control/logout.php"  title="<?php echo $MEGA_LANG['BYE_TITL']; ?>"><img src="/megacms/core/style/icons/logout.svg"/></a>
                </div>
            </div>
    </div>
    </div>
	<div id="menu">
	<div>
    <h1><?php echo $MEGA_LANG['SET']; ?></h1>
    <p>megacms <?php echo $MEGA_LANG['SET_VERS']; ?> 0.7 <a href="http://megacms.rusciori.org/updates.php" class="MEGA_btn"><?php echo $MEGA_LANG['SET_UPD']; ?></a> <a href="mailto:megacms@rusciori.org" class="MEGA_btn"><?php echo $MEGA_LANG['SET_REP']; ?></a> </p>
    <?php
    if($error != ""){
      echo '
      <div class="settings" style="background-color: #fc462a; color: white;">
        <span>'.$error.'</span>
      </div>
      ';
    }
     ?>

      <div class="settings" style="background-color: #fff187;">
          <a class="MEGA_folder MEGA_folder_active"><?php echo $MEGA_LANG['MEGA_SURV']; ?></a>
          <div>
            <?php echo $MEGA_LANG['MEGA_SURV_TXT']; ?>
          </div>
      </div>
      <?php
        if($_SESSION['megacms_user'] == 'admin'){
          echo '<div class="settings">
              <a class="MEGA_folder">'.$MEGA_LANG['SET_SYST'].'</a>
              <form action="" method="post" class="MEGA_form">
                <fieldset>
                  <table>
                    <tr>
                      <td>
                        '.$MEGA_LANG['SET_SYST_LANG'].'
                      </td>
                      <td>
                        <select name="lang">';

                            foreach (MEGA_language_available() as $value) {
                              $value = htmlentities(basename($value, ".json"));
                              $selected = "";
                              if($value== $MEGA_S['lang']) $selected= "selected=\"selected\"";
                              echo "
                              <option value=\"".$value."\" ".$selected.">
                                ".strtoupper($value)."
                              </option>";
                            }

                        echo '</select>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        '.$MEGA_LANG['SET_SYST_CHAR'].'
                      </td>
                      <td>
                        <input type="text" name="charset" value="'.$MEGA_S['charset'].'"/>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        '.$MEGA_LANG['SET_SYST_HIST'].'
                      </td>
                      <td>
                        <input type="number" min="1" name="history" value="'.$MEGA_S['history'].'" />
                      </td>
                    </tr>
                    <tr>
                      <td>
                        '.$MEGA_LANG['SET_SYST_HTPS'].'
                      </td>
                      <td>
                        <input type="checkbox" name="httpsonly" '.($MEGA_S['httpsonly'] ? "checked" : "").'/>
                      </td>
                    </tr>
                  </table>
                </fieldset>
                <input type="hidden" value="change_syst" name="type" />
                <input type="submit" value="'.$MEGA_LANG['FORM_OK'].'"  />
              </form>
          </div>
          <div class="settings">
              <a class="MEGA_folder">'.$MEGA_LANG['SET_UPL'].'</a>
              <form action="" method="post" class="MEGA_form">
                <fieldset>
                  <table>
                    <tr>
                      <td>
                        '.$MEGA_LANG['SET_UPL_DIR'].'
                      </td>
                      <td>
                        <input type="text" name="up_dir" value="'.$MEGA_S['up_dir'].'"/>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        '.$MEGA_LANG['SET_UPL_IMGS'].'
                      </td>
                      <td>
                        <input type="number" min="1" name="img_size" value="'.$MEGA_S['img_size'].'" />
                      </td>
                    </tr>
                    <tr>
                      <td>
                        '.$MEGA_LANG['SET_UPL_IMGQ'].'
                      </td>
                      <td>
                        <input type="number" min="1" max="100" name="img_quality" value="'.$MEGA_S['img_quality'].'" />
                      </td>
                    </tr>
                  </table>
                </fieldset>
                <input type="hidden" value="change_upl" name="type" />
                <input type="submit" value="'.$MEGA_LANG['FORM_OK'].'"  />
              </form>
          </div>
          <div class="settings">
              <a class="MEGA_folder">'.$MEGA_LANG['ED'].'</a>
              <form action="" method="post" class="MEGA_form">
                <fieldset>
                  <table>
                    <tr>
                      <td>
                        '.$MEGA_LANG['SET_ED_BLC'].'
                      </td>
                      <td>
                        <input type="text" name="ed_block" value="'.$MEGA_S['ed_block'].'"/>
                      </td>
                    </tr>
                  </table>
                </fieldset>
                <input type="hidden" value="change_ed" name="type" />
                <input type="submit" value="'.$MEGA_LANG['FORM_OK'].'"  />
              </form>
          </div>';
        }
       ?>
      <div class="settings">
          <a class="MEGA_folder"><?php echo $MEGA_LANG['SET_PW']; ?></a>
          <form action="" method="post" class="MEGA_form">
            <fieldset>
              <input type="password" placeholder="<?php echo $MEGA_LANG['SET_PW_OLD']; ?>" name="pw" />
              <input type="password" placeholder="<?php echo $MEGA_LANG['SET_PW_NEW']; ?>"  name="new"/>
              <input type="password" placeholder="<?php echo $MEGA_LANG['SET_PW_NEW2']; ?>" name="new2" />
              <input type="hidden" value="change_pw" name="type" />
            </fieldset>
            <input type="submit" value="<?php echo $MEGA_LANG['FORM_OK']; ?>"  />
          </form>
      </div>
      <div class="settings">
          <a class="MEGA_folder"><?php echo $MEGA_LANG['SET_MAIL']; ?></a>
          <form action="" method="post" class="MEGA_form">
            <fieldset>
              <input type="email" placeholder="<?php echo $MEGA_LANG['SET_MAIL_OLD']; ?>"  name="old"/>
              <input type="email" placeholder="<?php echo $MEGA_LANG['SET_MAIL_NEW']; ?>" name="new" />
              <input type="password" placeholder="<?php echo $MEGA_LANG['LGN_PW']; ?>"  name="pw"/>
              <input type="hidden" value="change_mail" name="type" />
            </fieldset>
            <input type="submit" value="<?php echo $MEGA_LANG['FORM_OK']; ?>"  />
          </form>
      </div>

	</div>
	</div>

</body>
</html>
