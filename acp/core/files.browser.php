<?php

//prohibit unauthorized access
require("core/access.php");

$deleteFile = strip_tags(basename($_GET['delete']));

if(isset($_GET[d])) {
	$_SESSION[disk] = (int) $_GET[d];
}

if(isset($_SESSION[disk])) {
	$disk = "$_SESSION[disk]";
}

if($_SESSION[sort_by_name] == '' AND $_SESSION[sort_by_size] == '' AND $_SESSION[sort_by_time] == '') {
	$_SESSION[sort_by_name] = 'ASC';
}


if(isset($_GET[sort_by_name])) {
	switch_sort('sort_by_name');
	unset($_SESSION[sort_by_size],$_SESSION[sort_by_time]);
}

if(isset($_GET[sort_by_size])) {
	switch_sort('sort_by_size');
	unset($_SESSION[sort_by_name],$_SESSION[sort_by_time]);
}

if(isset($_GET[sort_by_time])) {
	switch_sort('sort_by_time');
	unset($_SESSION[sort_by_size],$_SESSION[sort_by_name]);
}


$sort_direction = constant(trim('SORT_'.$_SESSION[sort_by_name].$_SESSION[sort_by_time].$_SESSION[sort_by_size]));

function switch_sort($session_name) {
	if($_SESSION[$session_name] == 'ASC') {
		$_SESSION[$session_name] = 'DESC';
	} else {
		$_SESSION[$session_name] = 'ASC';
	}
}

function show_sort_arrow($direction) {
	$icon = '';
	if($direction == 'ASC') {
		$icon = "<i class='icon-chevron-up'></i>";
	} elseif($direction == 'DESC') {
		$icon = "<i class='icon-chevron-down'></i>";
	}
	return $icon;
}


if($disk == "2") {
	$path = "../content/files";
	$disk2_class = "btn btn-primary btn-small";
	$disk1_class = "btn btn-small";
} else {
	$path = "../content/images";
	$disk1_class = "btn btn-primary btn-small";
	$disk2_class = "btn btn-small";
}

/* expand filter */
if($_POST[img_filter] != "") {
	$_SESSION[img_filter] = $_SESSION[img_filter] . ' ' . $_POST[img_filter];
}

/* remove keyword from filter list */
if($_REQUEST[rm_keyword] != "") {
	$all_filter = explode(" ", $_SESSION[img_filter]);
	unset($_SESSION[img_filter],$f);
	foreach($all_filter as $f) {
		if($_REQUEST[rm_keyword] == "$f") { continue; }
		if($f == "") { continue; }
		$_SESSION[img_filter] .= "$f ";
	}
	unset($all_filter);
}

if($_SESSION[img_filter] != "") {
	unset($all_filter);
	$all_filter = explode(" ", $_SESSION[img_filter]);
	foreach($all_filter as $f) {
		if($_REQUEST[rm_keyword] == "$f") { continue; }
		if($f == "") { continue; }
		$btn_remove_keyword .= "<a class='btn btn-small' href='acp.php?tn=filebrowser&sub=browse&rm_keyword=$f'><i class='icon-remove'></i> $f</a> ";
	}
	
}

$kw_form  = "<form action='acp.php?tn=filebrowser&sub=browse&d=$disk' method='POST' class='form-inline' style='margin-bottom:3px;'>";
$kw_form .= '<input class="input-block-level" type="text" name="img_filter" value="" placeholder="Filter">';
$kw_form .= '</form>';

echo '<div class="well well-small" style="margin-bottom:10px;">';
echo '<div class="row-fluid">';
echo '<div class="span12">';

echo '<div class="btn-toolbar">';
echo '<div class="btn-group">';
echo "<a class='$disk1_class' href='$_SERVER[PHP_SELF]?tn=filebrowser&sub=browse&d=1'>Grafiken</a> ";
echo "<a class='$disk2_class' href='$_SERVER[PHP_SELF]?tn=filebrowser&sub=browse&d=2'>Dateien</a>";
echo '</div>';
echo '<div class="btn-group">';
echo "<a class='btn btn-small' href='$_SERVER[PHP_SELF]?tn=filebrowser&sub=browse&d=$disk&sort_by_name=1'>". show_sort_arrow($_SESSION[sort_by_name]) ." $lang[filename]</a>";
echo "<a class='btn btn-small' href='$_SERVER[PHP_SELF]?tn=filebrowser&sub=browse&d=$disk&sort_by_time=1'>". show_sort_arrow($_SESSION[sort_by_time]) ." $lang[date_of_change]</a>";
echo "<a class='btn btn-small' href='$_SERVER[PHP_SELF]?tn=filebrowser&sub=browse&d=$disk&sort_by_size=1'>". show_sort_arrow($_SESSION[sort_by_size]) ." $lang[filesize]</a>";
echo '</div>';
echo '</div>';

echo '</div>';
echo '</div>';

echo '<div class="row-fluid">';
echo '<div class="span2">';
echo "$kw_form";
echo '</div>';
echo '<div class="span10">';
echo "$btn_remove_keyword";
echo '</div>';
echo '</div>';
echo '</div>';



/* DELETE FILE OR IMAGE */
if($deleteFile !== "") {

	if(is_file("$path/$deleteFile")) {
	
		if(unlink("$path/$deleteFile")) {
			echo"<div class='alert alert-success'>$lang[msg_file_delete]</div>";
		} else {
			echo"<div class='alert alert-error'>$lang[msg_file_delete_error]</div>";
		}
		
	} else {
		echo"<div class='alert alert-error'>File not found</div>";
	}

}



$a_files = scandir("$path");

$fileinfo = array();
$x=0;


foreach($a_files as $file) {

	/* no files like . or .. or .filename */ 
	if((substr($file, 0, 1) == ".")) {
		continue;
	}
	
	if($file == 'index.html') {
		continue;
	}
	
	
	if(is_array($all_filter)) {
	foreach($all_filter as $search_needle) {
		if($search_needle != '') {
	 if(stristr($file, $search_needle) == FALSE) { 
        continue 2;
   }
   }
	}
	}

	
	$f_suffix = substr (strrchr ($file, "."), 1 );
	$f_time = filemtime("$path/$file");
	$f_size =  filesize("$path/$file");
	$imgsize = getimagesize("$path/$file");
	
	if($imgsize[0] > 0) {
		$fileinfo[$x][filetype] = "image";
	} else {
		$fileinfo[$x][filetype] = "other";
	}
	
	$fileinfo[$x][filename] = "$file";
	$fileinfo[$x][size] = "$f_size";
	$fileinfo[$x][suffix] = "$f_suffix";
	$fileinfo[$x][time] = "$f_time";
	
	$x++;
} // eol foreach 


//count all files
$nbr_of_files = count($fileinfo);

/* sorting */

foreach ($fileinfo as $key => $row) {
    $fi_filename[$key]    = $row['filename'];
    $fi_size[$key] = $row['size'];
    $fi_time[$key] = $row['time'];
}


if($_SESSION[sort_by_name] != "") {
	@array_multisort($fi_filename, $sort_direction, $fileinfo);
} elseif($_SESSION[sort_by_size] != "") {
	@array_multisort($fi_size, $sort_direction, $fileinfo);
} elseif($_SESSION[sort_by_time] != "") {
	@array_multisort($fi_time, $sort_direction, $fileinfo);
}

$files_per_page = 20;
$show_numbers = 6;
$start = 0;

$cnt_pages = ceil($nbr_of_files/$files_per_page);


if(isset($_GET[start])) {
	$start = (int) $_GET[start];
}

if($start<0) {
	$start = 0;
}


$end = $start+$files_per_page;
$next_start = $start+$files_per_page;
$prev_start = $start-$files_per_page;

if($start>($nbr_of_files-$files_per_page)) {
	$next_start = $start;
}

if($end>$nbr_of_files) {
	$end = $nbr_of_files;
}



$pag_backlink = "<a class='buttonLink' href='$_SERVER[PHP_SELF]?tn=filebrowser&start=$prev_start'>$lang[pagination_backward]</a>";
$pag_forwardlink = "<a class='buttonLink' href='$_SERVER[PHP_SELF]?tn=filebrowser&start=$next_start'>$lang[pagination_forward]</a>";


unset($pag_string);
for($x=0;$x<$cnt_pages;$x++) {

	$aclass = "buttonLink";
	$page_start = $x*$files_per_page;
	$page_nbr = $x+1;
	
	if($page_start == $start) {
		$aclass = "buttonLink_sel";
		
		$pag_start = 	$x-($show_numbers/2);
		
		if($pag_start < 0) {
			$pag_start = 0;
		}
		
		$pag_end = 		$pag_start+$show_numbers;
		if($pag_end > $cnt_pages) {
			$pag_end = $cnt_pages;
		}
	}
	
	$a_pag_string[] = "<a class='$aclass' href='$_SERVER[PHP_SELF]?tn=filebrowser&start=$page_start'>$page_nbr</a> ";

} //eol for $x


echo"<div id='filesbox' class='clearfix'>";

if($disk == "2") {
	$tpl_file = file_get_contents('templates/list-files-grid.tpl');
} else {
	$tpl_file = file_get_contents('templates/list-files-thumbs.tpl');
}
//list all files 
for($i=$start;$i<$end;$i++) {

	unset($filename);

	$filename = $fileinfo[$i][filename];
	$filetime = $fileinfo[$i][time];
	$filesize = round($fileinfo[$i][size] / 1024) . ' KB';
	$filesize = readable_filesize($fileinfo[$i][size]);
	$show_filetime = date('d.m.Y H:i',$filetime);

	if($fileinfo[$i][filetype] == "image") {
		$set_style = '';
		$preview_button = "<a href='$path/$filename' title='$path/$filename' class='btn btn-mini fancybox'>$lang[preview]</a>";
		$preview_img = "<img src='$path/$filename' style='max-width:80px;max-height:80px;'>";
	} else {
		$set_style = "background-image: url(images/no-preview.gif); background-position: center; background-repeat: no-repeat;";
		$preview_button = "<a href='#' class='btn btn-mini disabled'>Vorschau</a>";
		$preview_img = '';
	}

	
	$delete_button = "<a href='acp.php?tn=filebrowser&sub=browse&delete=$filename&d=$disk&start=$start' onclick=\"return confirm('$lang[confirm_delete_file]')\" class='btn btn-danger btn-small btn-mini'>$lang[delete]</a></span>";

	$tpl_list = str_replace('{filename}', "$filename", $tpl_file);
	$tpl_list = str_replace('{set_style}', "$set_style", $tpl_list);
	$tpl_list = str_replace('{preview_img}', "$preview_img", $tpl_list);
	$tpl_list = str_replace('{show_filetime}', "$show_filetime", $tpl_list);
	$tpl_list = str_replace('{filesize}', "$filesize", $tpl_list);
	$tpl_list = str_replace('{preview_button}', "$preview_button", $tpl_list);
	$tpl_list = str_replace('{delete_button}', "$delete_button", $tpl_list);
	
	echo $tpl_list;

} // eol $i - list all files 



echo"</div>"; // eol div filesbox


echo"<div id='pagina'><p>";
echo"$pag_backlink ";
foreach(range($pag_start, $pag_end) as $number) {
    echo "$a_pag_string[$number]";
}
echo" $pag_forwardlink";
echo"</p></div>\n"; //EOL PAGINATION


?>