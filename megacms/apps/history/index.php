<?php
if (!defined('MEGA_ROOT')) define("MEGA_ROOT", realpath(__DIR__."/../../../"));
//packages
require_once(MEGA_ROOT.'/megacms/core/inc/db.php');
require_once(MEGA_ROOT.'/megacms/core/inc/security.php');
require_once(MEGA_ROOT.'/megacms/core/inc/settings.php');

MEGA_session();


if(MEGA_login_verify() != true){
    header('Location: /megacms');
    exit;
}

$editing = true;
$status = false;
$input = "";
$items = "";
try{
    function hist_output($path){
        $items = MEGA_get_history($path);
        $output_items = [];
        if($_SESSION['megacms_user'] !== "admin"){

          foreach($items as $i){
            if($i['user_id'] != $_SESSION['megacms_id']){
              break;
            }
            $output_items[] = $i;
          }
        }else{
          $output_items = $items;
        }
        if(sizeof($output_items) == 0){
            throw new Exception($GLOBALS['MEGA_LANG']['ERR_404_EMPT']);
        }

        $input =   "<form action='' method='post' class=\"MEGA_form\"><fieldset><table>";
        foreach($output_items as $i){
            $input = $input.'<tr>
              <td>
                <input type="radio" id="'.htmlentities($i['id']).'" name="id" value="'.htmlentities($i['id']).'">
              </td>
              <td>
                <label for="'.htmlentities($i['id']).'">'.htmlentities(date("d.m.Y H:i", strtotime($i['time'].' UTC'))." UTC").'</label>
              </td>
              <td>
                '.htmlentities($i['mail']).'
              </td>
            </tr>';
        }
        $input = $input.'</table></fieldset><input class="MEGA_btn" type="submit" value="'.$GLOBALS['MEGA_LANG']['FORM_SAVE'].'"/><a class="MEGA_btn" href="/megacms/control/cms.php?path='.rawurlencode(htmlentities($path)).'">'.$GLOBALS['MEGA_LANG']['FORM_DISM'].'</a></form>';
        return $input;
    }


    if(isset($_POST['id'])){//after submit
        $id = $_POST['id'];
        if(filter_var($id,FILTER_VALIDATE_INT) === false){
            throw new Exception($MEGA_LANG['ERR_INP']);
        }
        $reset = MEGA_history_reset($id);
        $path = $reset['task'];
        $editing = megacms_task_check($path,'notloggedin');
        if($editing == true){
          if($_SESSION['megacms_id'] != $id AND $_SESSION['megacms_user'] != "admin"){
              throw new Exception($MEGA_LANG['ERR_AUTH_ADM']);
          }
          megacms_history($path,$reset['code']);
          file_put_contents(realpath(MEGA_ROOT.$path),$reset['code']);
          header('Location: /megacms/control/cms.php?path='.rawurlencode($path));
          exit;
        }else{
          $input = hist_output($path);
        }

    }elseif(isset($_GET['file'])){ //before submit
        $path= rawurldecode($_GET['file']);

        $path = MEGA_repair_url($path);
        if($path == false){
          throw new Exception($MEGA_LANG['ERR_INP_URL']);
        }

        $input = hist_output($path);

    }else{
        throw new Exception($MEGA_LANG['ERR_INP']);
    }

    if($editing == false){
        $input = "<span style='color: red;'>".$MEGA_LANG['ERR_TASK_SAVE']."</span><br />".$input;
    }


}catch(Exception $e){
    $input = $MEGA_LANG['ERR'].$e->getMessage()."<a id=\"form_cancel\" class=\"MEGA_btn\" href=\"/megacms/control/cms.php\">".$MEGA_LANG['FORM_DISM']."</a>";
}
?>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $MEGA_LANG['HIST']; ?> | megacms</title>
    <script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>

    <script src="/megacms/core/js/ui.js"></script>

    <link href="/megacms/control/css/backend_style.css" rel="stylesheet" type="text/css"/>
    <link href="/megacms/core/style/css/stylesheet.css" rel="stylesheet" type="text/css"/>
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

                </div>
            </div>
    </div>
    </div>
	<div id="menu">
    	<div>
            <h1><?php echo $MEGA_LANG['HIST']; ?></h1>
            <?php
            echo $input;
                ?>

    	</div>
	</div>

</body>
</html>
