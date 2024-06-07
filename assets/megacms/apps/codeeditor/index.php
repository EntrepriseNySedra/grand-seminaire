<?php
if (!defined('MEGA_ROOT')) define("MEGA_ROOT", realpath(__DIR__."/../../../"));
//packages
require_once(MEGA_ROOT.'/megacms/core/inc/db.php');
require_once(MEGA_ROOT.'/megacms/core/inc/security.php');
require_once(MEGA_ROOT.'/megacms/core/inc/settings.php');

MEGA_session();


if(MEGA_login_verify()!= true OR $_SESSION['megacms_user'] != "admin"){
    header('Location: /megacms');
    exit;
}

$error = false;
$status = false;
$input = "";
try{

    if($_SESSION['megacms_user'] != "admin"){
      throw new Exception($MEGA_LANG['ERR_AUTH_ADM']);
    }

    if(isset($_POST['content']) AND isset($_POST['path'])){//after submit
        $path = rawurldecode($_POST['path']);
        $path = MEGA_repair_url($path);

        if(megacms_task_check($path,'loggedin')==false){
            $error = $MEGA_LANG['ERR_AUTH'];
        }

        $code = $_POST['content'];
        $status = "posted";
    }elseif(isset($_GET['file'])){ //before submit
        $path= rawurldecode($_GET['file']);
        $path = MEGA_repair_url($path);
        if($path == false){
          throw new Exception($MEGA_LANG['ERR_INP_URL']);
        }

        if(megacms_task_check($path,'notloggedin')==false){
            throw new Exception($MEGA_LANG['ERR_TASK']);
        }

        $code = file_get_contents(MEGA_ROOT.$path);
        if($code == ""){
            throw new Exception($MEGA_LANG['ERR_404_NOTF']);
        }

        if(megacms_task_insert($path,$code)==false){
            throw new Exception($MEGA_LANG['ERR_404_DB']);
        }

        $status = "requested";
    }else{
        throw new Exception($MEGA_LANG['ERR_INP']);
    }


    if($status == "posted" AND $error == false){
        megacms_history($path,$_POST['content']);
        $insert = $_POST['content'];
        $insert = htmlspecialchars_decode($insert,ENT_COMPAT | ENT_HTML5);
        $insert = iconv('UTF-8',$GLOBALS['MEGA_S']['charset'].'//TRANSLIT',$insert);
        file_put_contents(realpath(MEGA_ROOT.$path),$insert);
        megacms_task_delete($path);
        header('Location: /megacms/control/cms.php?path='.rawurlencode(htmlentities($path)));
        exit;
    }else{
      $code = iconv($GLOBALS['MEGA_S']['charset'],'UTF-8'.'//TRANSLIT',$code);
      $input =   $MEGA_LANG['CODE_FILE'].": ".htmlentities($path)."<form action=\"index.php\" method=\"post\" class=\"MEGA_form\">
          <textarea id='textarea' name=\"content\">".htmlspecialchars($code,ENT_COMPAT | ENT_HTML5, 'UTF-8')."</textarea>
          <input  type=\"hidden\" value=\"".htmlentities($path)."\" name=\"path\"/>
          <input  class= \"MEGA_btn\" type=\"submit\" value=\"".$MEGA_LANG['FORM_SAVE']."\"/>
          <a class= \"MEGA_btn\"  href=\"/megacms/control/cms.php?path=".rawurlencode(htmlentities($path))."\">".$MEGA_LANG['FORM_DISM']."</a>
        </form>";

      if($error == true){
          $input = "<span style='color: red;'>".$error."</span><br />".$input;
      }
    }
}catch(Exception $e){
    $input = $MEGA_LANG['ERR'].htmlentities($e->getMessage(),ENT_COMPAT | ENT_HTML5,'UTF-8')."<a id=\"form_cancel\" class=\"MEGA_btn\" href=\"/megacms/control/cms.php\">".$MEGA_LANG['FORM_DISM']."</a>";
}
?>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $MEGA_LANG['CODE']; ?> | megacms</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>

    <script src="/megacms/core/js/ui.js"></script>
    <script src="/megacms/apps/codeeditor/js/code.js" ></script>

    <script src="/megacms/apps/codeeditor/codemirror/lib/codemirror.js"></script>
    <link rel="stylesheet" href="/megacms/apps/codeeditor/codemirror/lib/codemirror.css">
    <script src="/megacms/apps/codeeditor/codemirror/mode/php/php.js"></script>
    <script src="/megacms/apps/codeeditor/codemirror/mode/xml/xml.js"></script>
    <script src="/megacms/apps/codeeditor/codemirror/mode/javascript/javascript.js"></script>
    <script src="/megacms/apps/codeeditor/codemirror/mode/htmlmixed/htmlmixed.js"></script>
    <script src="/megacms/apps/codeeditor/codemirror/mode/clike/clike.js"></script>

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
            <h1><?php echo $MEGA_LANG['CODE']; ?></h1>
            <?php
            echo $input;
                ?>

    	</div>
	</div>
  <script>
    var myCodeMirror = CodeMirror.fromTextArea(document.getElementById("textarea"),{
      lineNumbers: true,
      mode: "php"
    });
  </script>
</body>
</html>
