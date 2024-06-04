<?php
if (!defined('MEGA_ROOT')) define("MEGA_ROOT", realpath(__DIR__."/../../"));
require_once(MEGA_ROOT.'/megacms/core/inc/db.php');
require_once(MEGA_ROOT.'/megacms/core/inc/security.php');
require_once(MEGA_ROOT.'/megacms/core/inc/settings.php');

MEGA_session();

if(MEGA_login_verify() != true){
  header('Location: /megacms');
  exit;
}

if(!isset($_GET['path'])){
  $path = "";
}else{
  $path = $_GET['path'];
}
 ?>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $MEGA_LANG['CMS']; ?> | MEGACMS</title>
    <script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
    <script type="text/javascript" src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="/megacms/core/js/ui.js" type="text/javascript"></script>
    <script src="/megacms/control/js/cms.js" type="text/javascript"></script>
    <link href="/megacms/core/style/css/stylesheet.css" rel="stylesheet" type="text/css"/>
    <link href="/megacms/control/css/backend_style.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="shortcut icon" type="image/png" href="/megacms/favicon.png"/>
    <link rel="manifest" href="/megacms/core/style/icons/favicon/manifest.json">
    <meta name="viewport" content="width=device-width" />
</head>
<body >

    <div id="navigation">
        <div id="nav_container">
            <div id="sidebar">
                <div>
                    <a href="https://mega.mg/" title="MEGA" style="position: absolute;top: 0;"><img src="https://cdn.mega.mg/img/favicon.png"></a>
                    <a href="/megacms/control/cms.php"  title="<?php echo $MEGA_LANG['CMS']; ?>"><i class="fa fa-home"></i></a>
                    <a href="/megacms/control/cms.php?path=%2Fheader.php"  title="Header"><i class="fa fa-chevron-up"></i></a>
                    <a href="/megacms/control/cms.php?path=%2Ffooter.php"  title="Footer"><i class="fa fa-chevron-down"></i></a>
                    <?php echo $_SESSION['megacms_user'] == 'admin' ? '<a href="/megacms/control/users.php" title="'.$MEGA_LANG['USR'].'"><i class="fa fa-user-o"></i></a>' : ''  ?>
                    <a href="/megacms/control/logout.php"  title="<?php echo $MEGA_LANG['BYE_TITL']; ?>"><i class="fa fa-power-off"></i></a>
                </div>
            </div>
        <div id="explorer">
          <div id="file_explorer">
            <h1><?php echo $MEGA_LANG['CMS_FILE']; ?></h1>
            <?php
                //output files
		              require(MEGA_ROOT."/megacms/control/fileexplorer.php");
	                $iframepath = "/";
                  MEGA_display_dir($_SESSION["megacms_files"],"");
	                $edited = false;
	                if($path != ""){
                      $iframepath = htmlentities(strip_tags(rawurldecode($path)));
                      $edited = true;
                  }
		        ?>
		        </ul>
          </div>
          <div id="options_explorer">
            <h1><?php echo $MEGA_LANG['CMS_OPT']; ?></h1>
            <ul>
              <?php
                if($_SESSION['megacms_user'] == 'admin'){
                  echo '<li>
                    <a id="areaselector">'.$MEGA_LANG['AREA'].'</a>
                  </li>';
                }
               ?>
              <?php
                if($_SESSION['megacms_user'] == 'admin'){
                  echo '<li>
                    <a id="code">'.$MEGA_LANG['CODE'].'</a>
                  </li>';
                }
               ?>
              <li>
                <a id="history"><?php echo $MEGA_LANG['HIST']; ?></a>
              </li>
            </ul>
          </div>
        </div>
        </div>
        <div id="buttons">
           
        
        </div>
    </div>
	<div id="menu">
        <iframe id="frame" src="<?php echo $iframepath; echo "?megacms=".rand(1,1000);?>"></iframe>
	</div>
<script>

</script>
</body>
</html>
