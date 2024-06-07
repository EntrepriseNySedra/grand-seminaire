<?php
function MEGA_read_dir($url){
	$folders = [];
	$files = [];
	$glob_php = glob($url."/*.php");
	$glob_html = glob($url."/*.html");
	$glob_htm = glob($url."/*.htm");
	$glob_folder = glob($url."/*/");
	foreach (array_merge($glob_htm,$glob_php,$glob_html,$glob_folder) as $dir_entry) {
		$basename = basename($dir_entry);
		if(is_dir($dir_entry)){
			if(!in_array($basename, array('megacms'), true )){
				$folders[] = $basename;
			}
		}else{
			if(!strstr($basename, "cms_temp.php")){
				$files[] = $basename;
			}
		}
	}
	$return_folder = [];
	foreach ($folders as $folders_item) {
		$return_folder[$folders_item] = MEGA_read_dir($url."/".$folders_item);
	}
	$dir["folders"] = $return_folder;
	$dir["files"] = $files;
	return $dir;
}

function MEGA_display_dir($dir,$htmldir){
	echo "<ul id='".$htmldir."'>";
	foreach ($dir["files"] as $dir_item) {
		echo "<li class='megacms_file'><a data-url='".$htmldir."/".$dir_item."'>$dir_item</a></li>";
	}
	foreach ($dir["folders"] as $dir_item => $dir_value) {
		echo "<li><a class=\"MEGA_folder\">".$dir_item."</a>";
		MEGA_display_dir($dir_value,$htmldir."/".$dir_item);
		echo "</li>";
	}
	echo "</ul>";
}
?>
