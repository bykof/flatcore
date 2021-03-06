<?php


/**
 * Build the Mainmenu
 * get all pages where page_sort is integer
 *
 * @return	array
 */

function show_mainmenu() {

	global $fc_nav;
	global $current_page_sort;
	global $fc_mod_rewrite;
	global $fc_defs;
	
	$count_result = count($fc_nav);
	
	for($i=0;$i<$count_result;$i++) {
	
		if($fc_nav[$i][page_sort] == "" || $fc_nav[$i][page_sort] == 'portal') {
			continue; //no page_sort or portal -> no menu item
		}
		
		$sort = $fc_nav[$i][page_sort];
		$points_of_item = substr_count($sort, '.');
		
		if($points_of_item < 1) {
			$menu[$i][page_id] = $fc_nav[$i][page_id];
			$menu[$i][page_sort] = $fc_nav[$i][page_sort];
			$menu[$i][page_linkname] = stripslashes($fc_nav[$i][page_linkname]);
			$menu[$i][page_title] = stripslashes($fc_nav[$i][page_title]);
			$menu[$i][page_permalink] = $fc_nav[$i][page_permalink];
			
			$menu[$i][link_status] = "$fc_defs[main_nav_class]";
		
			if(left_string($current_page_sort) == left_string($menu[$i][page_sort]) ) {
				$menu[$i][link_status] = "$fc_defs[main_nav_class_active]";
				define('FC_MAIN_CAT', clean_filename($fc_nav[$i][page_linkname]));
				define('FC_TOC_HEADER', $menu[$i][page_linkname]);
			}
		
			/* generate the main menu */
			if($fc_mod_rewrite == "off") {
				$menu[$i][link] = "$_SERVER[PHP_SELF]?p=" . $fc_nav[$i][page_id];
			} elseif ($fc_mod_rewrite == "permalink") {
				$menu[$i][link] = FC_INC_DIR . "/" . $fc_nav[$i][page_permalink];
			}
		}
	}
	
	
	return $menu;

} // eol func show_menu




/**
 * Build the Submenu
 * get all pages where page_sort begins with the given number (also a page_sort)
 *
 * @param mixed $num (page_sort of parent page)
 * @return array
 */

function show_menu($num){

	global $fc_nav;
	
	if($num == "") { return; }
	
	$items = array();
	unset($sort);
	$points_of_num = substr_count($num, '.');
	
	if($points_of_num >= 0) {
	
		$str_length = strlen($num);
		$count_result = count($fc_nav);
	
		for($i=0;$i<$count_result;$i++) {
	
			$sort = $fc_nav[$i][page_sort];
			$sort_length = strlen($sort);
			$trim_actual_page = substr($num, 0, $sort_length);
	
			/* All Pages at this Level */
			if($sort == "$trim_actual_page") {
				$items = show_this_level($sort);
				if(is_array($items)){
					foreach($items as $value) {
	    			$m[] = $value;
					}
				}
			}
	
			/* Submenu */
			if($sort == $num) {
				$items = get_sm($sort);
				if(is_array($items)) {
					foreach($items as $value) {
	    			$m[] = $value;
					}
				}
			}
			
		}
	
	}
	
	return $m;
}


function get_sm($num){

	global $fc_nav;
	global $current_page_sort;
	global $fc_mod_rewrite;
	
	unset($sort);
	
	$points_of_num = substr_count($num, '.');
	$str_length = strlen($num);
	$count_result = count($fc_nav);
	
	for($i=0;$i<$count_result;$i++) {
	
		$sort = $fc_nav[$i][page_sort];
		$points_of_sort = substr_count($sort, '.');
		
		$trim_sort = substr($fc_nav[$i][page_sort], 0, $str_length);
		
		if($num == "$trim_sort") {
				
			if($points_of_sort == ($points_of_num+1)) {
				$submenu[$i][page_id] = $fc_nav[$i][page_id];
				$submenu[$i][page_sort] = $fc_nav[$i][page_sort];
				$submenu[$i][page_permalink] = $fc_nav[$i][page_permalink];
				$submenu[$i][page_linkname] = stripslashes($fc_nav[$i][page_linkname]);
				$submenu[$i][page_title] = stripslashes($fc_nav[$i][page_title]);
				$submenu[$i][link_status] = "sub_link$points_of_sort";
			
				/* genertate the submenu */
				if($fc_mod_rewrite == "off") {
					$submenu[$i][sublink] = "$_SERVER[PHP_SELF]?p=" . $fc_nav[$i][page_id];
				} elseif ($fc_mod_rewrite == "permalink") {
					$submenu[$i][sublink] = FC_INC_DIR . "/" . $fc_nav[$i][page_permalink];
				}
			}	
		}
	} // eol $i
	
	return $submenu;

} // eol func get_sm




function show_this_level($num) {

	global $fc_nav;
	global $current_page_sort;
	global $fc_mod_rewrite;
	global $fc_defs;
		
	unset($sort);
	
	$points_of_num = substr_count($num, '.');
	$str_length = strlen($num);
	
	if($points_of_num > 0) {
		
		$pre_num = substr($num, 0, (strlen ($num)) - (strlen (strrchr($num,'.'))));
		$pre_num_length = strlen($pre_num);
		
		$count_result = count($fc_nav);
		
		for($i=0;$i<$count_result;$i++) {
		
			$sort = $fc_nav[$i][page_sort];
			$sort_length = strlen($sort);
			$points_of_sort = substr_count($sort, '.');
			$trim_sort = substr($sort, 0, $pre_num_length);
		
			if($trim_sort != "$pre_num") {
				continue;
			}
		
			if(left_string($sort) != left_string($num)) {
				continue;
			}
		
			if($str_length == $sort_length) {
				$menu[$i][page_id] = $fc_nav[$i][page_id];
				$menu[$i][page_sort] = $fc_nav[$i][page_sort];
				$menu[$i][page_permalink] = $fc_nav[$i][page_permalink];
				$menu[$i][page_linkname] = stripslashes($fc_nav[$i][page_linkname]);
				$menu[$i][page_title] = stripslashes($fc_nav[$i][page_title]);
				$menu[$i][link_status] = "$fc_defs[sub_nav_prefix_class]".$points_of_sort;
		
				if($sort == $current_page_sort) {
					$menu[$i][link_status] = "$fc_defs[sub_nav_prefix_class_active]".$points_of_sort;
				}
		
				if($fc_mod_rewrite == "off") {
					$menu[$i][sublink] = "$_SERVER[PHP_SELF]?p=" . $fc_nav[$i][page_id];
				} elseif ($fc_mod_rewrite == "permalink") {
					$menu[$i][sublink] = FC_INC_DIR . "/" . $fc_nav[$i][page_permalink];
				}
			}
		}
	}
	return $menu;
}



/**
 * Build an unordered list <ul> with al pages and sub-pages
 * sort by page_sort
 * use {$fc_sitemap} in your templates
 * @return string
*/


function show_sitemap() {
	
	global $fc_nav;
	global $current_page_sort;
	global $fc_mod_rewrite;
	
	$cnt_results = count($fc_nav);
	
	$sm_string .= '<ul class="fc-sitemap">';
	
	for($i=0;$i<$cnt_results;$i++) {
	
		$page_id = $fc_nav[$i]['page_id'];
		$page_sort = $fc_nav[$i]['page_sort'];
		$page_linkname = $fc_nav[$i]['page_linkname'];
		$page_title = $fc_nav[$i]['page_title'];
		$page_status = $fc_nav[$i]['page_status'];
		$page_permalink = $fc_nav[$i]['page_permalink'];
			
		if($fc_nav[$i]['page_sort'] == "" || $fc_nav[$i]['page_sort'] == 'portal') {
			continue;
		}
		
		$points_of_item[$i] = substr_count($page_sort, '.');
		
		unset($next_level);
		if($points_of_item[$i] > $points_of_item[$i-1]) {
			$next_level = "<ul class='fc-sitemap-$points_of_item[$i]'>";
		}
			
		unset($end_level);
		if($points_of_item[$i] < $points_of_item[$i-1]) {
			$div_level = $points_of_item[$i] - $points_of_item[$i-1];
			$end_level = str_repeat("</ul>", abs($div_level));
		}
		
		if($fc_mod_rewrite == "permalink") {
			$target = FC_INC_DIR . "/" . $page_permalink;
		} else {
			$target = "$_SERVER[PHP_SELF]?p=$page_id";
		}
		
		$sm_string .= "
		$next_level
		$end_level
		<li><a href='$target' title='$page_title'>$page_linkname</a><span>$page_title ($current_page_sort)</span></li>";
	}

	$sm_string .= "</ul>";	
		
	return $sm_string;
}



function left_string($string) {
  $string = explode(".", $string);
  return $string[0];
}

?>