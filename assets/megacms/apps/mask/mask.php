<?php
if (!defined('MEGA_ROOT')) define("MEGA_ROOT", realpath(__DIR__."/../../../"));
//packages
require_once(MEGA_ROOT."/megacms/core/inc/settings.php");
require_once(MEGA_ROOT."/megacms/core/inc/security.php");
require_once(MEGA_ROOT."/megacms/core/inc/db.php");
require_once(MEGA_ROOT.'/megacms/core/inc/simple_html_dom.php');

MEGA_session();
if(MEGA_login_verify() != true){
    header('Location: /megacms');
    exit;
}
if($_SESSION['megacms_user'] != 'admin'){
  header('Location: /megacms');
  exit;
}

if(isset($_POST["saved"])){
    if(isset($_POST["id"]) AND isset($_POST["name"]) AND isset($_POST["code"])){
        $code = $_POST["code"];
        $code = str_replace(array("\r\n", chr(10).chr(13), "\r", "\n", PHP_EOL, chr(10), chr(13)),'--jo:r--', $code);
        $domobject = str_get_html ($code);
        $attr = "data-jo-content";
        $mask = $domobject->find("*", 0);
        $mask->$attr = "noneditable";
        $code = str_replace("--jo:r--", PHP_EOL,  $domobject->save());
        MEGA_set_mask($_POST["id"], $_POST["name"], "mask", $code);
    }
}
if(isset($_GET["deleted"]) AND isset($_GET["id"])){
    MEGA_delete_mask($_GET["id"]);
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $MEGA_LANG['MSK_TITL']; ?> | megacms</title>
    <script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
    <script type="text/javascript" src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="/megacms/core/js/ui.js"></script>
    <script src="/megacms/apps/mask/js/mask.js"></script>

    <script src="/megacms/apps/codeeditor/codemirror/lib/codemirror.js"></script>
    <link rel="stylesheet" href="/megacms/apps/codeeditor/codemirror/lib/codemirror.css">
    <script src="/megacms/apps/codeeditor/codemirror/mode/php/php.js"></script>
    <script src="/megacms/apps/codeeditor/codemirror/mode/xml/xml.js"></script>
    <script src="/megacms/apps/codeeditor/codemirror/mode/javascript/javascript.js"></script>
    <script src="/megacms/apps/codeeditor/codemirror/mode/htmlmixed/htmlmixed.js"></script>
    <script src="/megacms/apps/codeeditor/codemirror/mode/clike/clike.js"></script>

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
                    <a href="/megacms/control/cms.php"  title="<?php echo $MEGA_LANG['CMS_FILE']; ?>"><img src="/megacms/core/style/icons/home.svg"/></a>
                    <a id="active" href="/megacms/apps/mask/mask.php"  title="<?php echo $MEGA_LANG['MSK_TITL']; ?>"><img src="/megacms/core/style/icons/mask.svg"/></a>
                    <?php echo $_SESSION['megacms_user'] == 'admin' ? '<a href="/megacms/control/users.php" title="'.$MEGA_LANG['USR'].'"><img src="/megacms/core/style/icons/users.svg" /></a>' : ''  ?>
                    <a href="/megacms/control/settings.php" title="<?php echo $MEGA_LANG['SET']; ?>"><img src="/megacms/core/style/icons/settings.svg"/></a>
                    <a href="/megacms/control/logout.php"  title="<?php echo $MEGA_LANG['BYE_TITL']; ?>"><img src="/megacms/core/style/icons/logout.svg"/></a>
                </div>
            </div>
    </div>
    </div>
	<div id="menu">
	       <div>
               <h1><?php echo $MEGA_LANG['MSK_TITL']; ?></h1>
               <div class="column_container">
                   <div class="settings mask_list">
                       <a class="MEGA_btn"><img src="/megacms/core/style/icons/plus.svg" /> <?php echo $MEGA_LANG['MSK_NEW'] ?></a>
                       <?php
                            foreach (MEGA_get_masks("all") as $mask) {
                                echo "<a data-id=".$mask['id'].">".$mask['name']."</a>";
                            }
                        ?>
                   </div>
                   <div class="settings mask_set">
                       <form action="/megacms/apps/mask/mask.php" method="post" class="MEGA_form">
                           <table>
                               <tr>
                                   <td>
                                       <?php echo $MEGA_LANG['MSK_NME'] ?>
                                   </td>
                                   <td>
                                       <input id="name" type="text" name="name" placeholder="<?php echo $MEGA_LANG['MSK_NEW_NME'] ?>" value=""/>
                                   </td>
                               </tr>
                               <tr>
                                   <td>
                                       <?php echo $MEGA_LANG['MSK_CDE'] ?>
                                   </td>
                                   <td>
                                       <textarea id="textarea" name="code"></textarea>
                                   </td>
                               </tr>
                           </table>
                           <input type="hidden" name="saved" value="true" />
                           <input id="id" type="hidden" name="id" value="0" />
                           <input type="submit" value="<?php echo $MEGA_LANG['FORM_OK'] ?>" /><a id="delete" class="MEGA_btn" href=""><?php echo $MEGA_LANG['FORM_DEL'] ?></a>
                       </form>
                   </div>
               </div>
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
