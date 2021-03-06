<?php

//prohibit unauthorized access
require("core/access.php");


echo"\n <form id='editpage' action='$_SERVER[PHP_SELF]?tn=pages&sub=edit&editpage=$editpage' class='form-horizontal' method='POST'>\n";

$custom_fields = get_custom_fields();
sort($custom_fields);
$cnt_custom_fields = count($custom_fields);



echo '<ul class="nav nav-tabs" id="bsTabs">';
echo '<li class="active"><a href="#info" data-toggle="tab">'.$lang[tab_info].'</a></li>';
echo '<li><a href="#content" data-toggle="tab">'.$lang[tab_content].'</a></li>';
echo '<li><a href="#extracontent" data-toggle="tab">'.$lang[tab_extracontent].'</a></li>';
echo '<li><a href="#meta" data-toggle="tab">'.$lang[tab_meta].'</a></li>';
echo '<li><a href="#head" data-toggle="tab">'.$lang[tab_head].'</a></li>';
echo '<li><a href="#preferences" data-toggle="tab">'.$lang[tab_page_preferences].'</a></li>';
if($cnt_custom_fields > 0) {
	echo '<li><a href="#custom" data-toggle="tab">'.$lang[legend_custom_fields].'</a></li>';
}
echo '</ul>';




echo '<div class="tab-content">';

/* tab_info */
echo'<div class="tab-pane fade in active" id="info">';

$dbh = new PDO("sqlite:".CONTENT_DB);
$sql = "SELECT page_linkname, page_sort, page_title, page_language FROM fc_pages
		    WHERE page_sort != 'portal'
		    ORDER BY page_language ASC, page_sort ASC	";
$all_pages = $dbh->query($sql)->fetchAll();

$dbh = null;


$select_page_position  = '<select name="page_position">';
$select_page_position .= '<option value="null">' . $lang[legend_unstructured_pages] . '</option>';
$select_page_position .= '<option value="portal">' . $lang[f_homepage] . '</option>';

if($page_sort == "portal") {
	$select_page_position .= '<option value="portal" selected>' . $lang[f_homepage] . '</option>';
}

if(ctype_digit($page_sort)) {
	$select_page_position .= '<option value="mainpage" selected>'.$lang[f_mainpage].'</option>';
} else {
	$select_page_position .= '<option value="mainpage">'.$lang[f_mainpage].'</option>';
}

$select_page_position .= '<optgroup label="'.$lang[f_subpage].'">';
for($i=0;$i<count($all_pages);$i++) {

	if($all_pages[$i][page_sort] == $page_sort) {
		continue;
	}
	
	if($all_pages[$i][page_sort] == "") {
		continue;
	}
	
	if($pos = strripos($page_sort,".")) {
		$string = substr($page_sort,0,$pos);
	}
		 
		 $parent_string = $all_pages[$i][page_sort];
		 
		 unset($selected);
		 if($parent_string != "" && $parent_string == "$string") {
		 	$selected = "selected";
		 }
		 
	$short_title = first_words($all_pages[$i][page_title], 6);
	$indent = str_repeat("-",substr_count($parent_string,'.'));
	$select_page_position .= "<option value='$parent_string' $selected> $indent " . $all_pages[$i][page_sort] . ' - ' .$all_pages[$i][page_linkname] . ' - ' . $short_title ."</option>";
	
}
$select_page_position .= '</optgroup>';
$select_page_position .= '</select>';

echo tpl_form_control_group('',$lang[f_page_position],$select_page_position);

if($page_sort != "portal") {

	$page_order = substr (strrchr ($page_sort, "."), 1);
	if(ctype_digit($page_sort)) {
		$page_order = $page_sort;
	}
	
	echo tpl_form_control_group('',$lang[f_page_order],"<input class='span2' type='text' name='page_order' value='$page_order'>");
	

}
	
echo tpl_form_control_group('',$lang[f_page_linkname],"<input class='span5' type='text' name='page_linkname' value='$page_linkname'>");
echo tpl_form_control_group('',$lang[f_page_permalink],"<input class='span5' type='text' name='page_permalink' value='$page_permalink'>");

echo'</div>'; /* EOL tab_info */


/* tab_content */
echo'<div class="tab-pane fade" id="content">';

echo"<textarea name='page_content' class='mceEditor'>
	 $page_content
	 </textarea>";

echo"</div>";
/* EOL tab_content */


/* tab_extracontent */

echo'<div class="tab-pane fade" id="extracontent">';

echo"<textarea name='page_extracontent' class='mceEditor_small'>
	 $page_extracontent
	 </textarea>";

echo"</div>"; /* EOL tab_extracontent */



/* tab_meta */
echo'<div class="tab-pane fade" id="meta">';

echo tpl_form_control_group('',$lang[f_page_title],"<input class='span5' type='text' name='page_title' value='$page_title'>");

if($page_meta_author == "") {
	$page_meta_author = "$_SESSION[user_firstname] $_SESSION[user_lastname]";
}

echo tpl_form_control_group('',$lang[f_meta_author],"<input class='span5' type='text' name='page_meta_author' value='$page_meta_author'>");
echo tpl_form_control_group('',$lang[f_meta_keywords],"<input class='span5' type='text' name='page_meta_keywords' value='$page_meta_keywords'>");
echo tpl_form_control_group('',$lang[f_meta_description],"<textarea name='page_meta_description' class='span5 cntValues' rows='5'>$page_meta_description</textarea>");

		
$select_page_meta_robots  = '<select name="page_meta_robots" class="span3">';
$select_page_meta_robots .= '<option value="all" '.($page_meta_robots == "all" ? 'selected="selected"' :'').'>all</option>';
$select_page_meta_robots .= '<option value="noindex" '.($page_meta_robots == "noindex" ? 'selected="selected"' :'').'>noindex</option>';
$select_page_meta_robots .= '<option value="nofollow" '.($page_meta_robots == "nofollow" ? 'selected="selected"' :'').'>nofollow</option>';
$select_page_meta_robots .= '<option value="noodp" '.($page_meta_robots == "noodp" ? 'selected="selected"' :'').'>noodp</option>';
$select_page_meta_robots .= '<option value="noydir" '.($page_meta_robots == "noydir" ? 'selected="selected"' :'').'>noydir</option>';
$select_page_meta_robots .= '</select>';
echo tpl_form_control_group('',$lang[f_meta_robots],$select_page_meta_robots);



echo'</div>'; /* EOL tab_meta */



/* tab_head */
echo'<div class="tab-pane fade" id="head">';

echo "$lang[f_head_styles]";
echo '<span class="silent"> &lt;style type=&quot;text/css&quot;&gt;</span> ... <span class="silent">&lt;/styles&gt;</span>';
echo "<textarea name='page_head_styles' class='input-block-level' rows='10'>$page_head_styles</textarea>";

echo '<hr>';

echo "$lang[f_head_enhanced]";
echo '<span class="silent"> &lt;head&gt;</span> ... <span class="silent">&lt;/head&gt;</span>';
echo "<textarea name='page_head_enhanced' class='input-block-level' rows='10'>$page_head_enhanced</textarea>";

echo'</div>'; /* EOL tab_head */



/* tab_preferences */
echo'<div class="tab-pane fade" id="preferences">';

$arr_lang = get_all_languages();

$select_page_language  = '<select name="page_language" class="span3">';
for($i=0;$i<count($arr_lang);$i++) {

	$lang_sign = $arr_lang[$i][lang_sign];
	$lang_desc = $arr_lang[$i][lang_desc];
	$lang_folder = $arr_lang[$i][lang_folder];
	$select_page_language .= "<option value='$lang_folder'".($page_language == "$lang_folder" ? 'selected="selected"' :'').">$lang_sign ($lang_desc)</option>";	

} // eo $i

$select_page_language .= '</select>';

echo tpl_form_control_group('',$lang[f_page_language],$select_page_language);

$arr_Styles = get_all_templates();

$select_select_template = '<select id="select_template" name="select_template"  class="span3">';

if($page_template == '') {
	$selected_standard = 'selected';
}

$select_select_template .= "<option value='use_standard<|-|>use_standard' $selected_standard>$lang[use_standard]</option>";

/* templates list */
foreach($arr_Styles as $template) {


$arr_layout_tpl = glob("../styles/$template/templates/layout*.tpl");

$select_select_template .= "<optgroup label='$template'>";

foreach($arr_layout_tpl as $layout_tpl) {
	$layout_tpl = basename($layout_tpl);

	$selected = '';
	if($template == "$page_template" && $layout_tpl == "$page_template_layout") {
		$selected = 'selected';
	}
	
	$select_select_template .=  "<option $selected value='$template<|-|>$layout_tpl'>$template » $layout_tpl</option>";
}

$select_select_template .= '</optgroup>';
    



} // eo foreach template list

$select_select_template .= '</select>';

echo tpl_form_control_group('',$lang[f_page_template],$select_select_template);


$arr_iMods = get_all_moduls();

$select_page_modul = '<select name="page_modul"  class="span3">';

$select_page_modul .= '<option value="">Kein Modul</option>';

for($i=0;$i<count($arr_iMods);$i++) {

	$selected = "";

	$mod_name = $arr_iMods[$i][name];
	$mod_folder = $arr_iMods[$i][folder];

	if($mod_folder == "$page_modul") {
		$selected = "selected";
	}

	$select_page_modul .= "<option value='$mod_folder' $selected>$mod_name</option>";

} // eo $i


$select_page_modul .= '</select>';


echo tpl_form_control_group('',$lang[f_page_modul],$select_page_modul);
		
echo tpl_form_control_group('',$lang[f_page_modul_query],"<input class='span5' type='text' name='page_modul_query' value='$page_modul_query'>");


unset($checked_status);

if($page_status == "") {
	$page_status = "public";
}

$select_page_status = '<label class="radio">';
$select_page_status .= "<input type='radio' name='page_status' value='public'".($page_status == "public" ? 'checked' :'')."> <span class='label label-success'>$lang[f_page_status_puplic]</span>";
$select_page_status .= '</label>';

$select_page_status .= '<label class="radio">';
$select_page_status .= "<input type='radio' name='page_status' value='private'".($page_status == "private" ? 'checked' :'')."> <span class='label label-important'>$lang[f_page_status_private]</span>";
$select_page_status .= '</label>';

$select_page_status .= '<label class="radio">';
$select_page_status .= "<input type='radio' name='page_status' value='draft'".($page_status == "draft" ? 'checked' :'')."> <span class='label'>$lang[f_page_status_draft]</span>";	
$select_page_status .= '</label>';

echo tpl_form_control_group('',$lang[f_page_status],$select_page_status);




$arr_groups = get_all_groups();
$arr_checked_groups = explode(",",$page_usergroup);

for($i=0;$i<count($arr_groups);$i++) {

	$group_id = $arr_groups[$i][group_id];
	$group_name = $arr_groups[$i][group_name];

	if(in_array("$group_name", $arr_checked_groups)) {
		$checked = "checked";
	} else {
		$checked = "";
	}
	
	$checkbox_usergroup .= '<label class="checkbox">';
	$checkbox_usergroup .= "<input type='checkbox' $checked name='set_usergroup[]' value='$group_name'> $group_name";
	$checkbox_usergroup .= '</label>';
}


echo tpl_form_control_group('',$lang[choose_usergroup],$checkbox_usergroup);



$arr_admins = get_all_admins();

$arr_checked_admins = explode(",", $page_authorized_users);

$cnt_admins = count($arr_admins);


for($i=0;$i<$cnt_admins;$i++) {

	$user_nick = $arr_admins[$i][user_nick];

  if(in_array("$user_nick", $arr_checked_admins)) {
		$checked_user = "checked";
	} else {
		$checked_user = "";
	}
		
	$checkbox_set_authorized_admins .= '<label class="checkbox">';
 	$checkbox_set_authorized_admins .= "<input type='checkbox' $checked_user name='set_authorized_admins[]' value='$user_nick'> $user_nick";
 	$checkbox_set_authorized_admins .= '</label>';
}

echo tpl_form_control_group('',$lang[f_page_authorized_admins],$checkbox_set_authorized_admins);



echo'</div>'; /* EOL tab_preferences */



if($cnt_custom_fields > 0) {

/* tab custom fields */
echo'<div class="tab-pane fade" id="custom">';

	for($i=0;$i<$cnt_custom_fields;$i++) {	
		if(substr($custom_fields[$i],0,10) == "custom_one") {
			$label = substr($custom_fields[$i],11);
			echo tpl_form_control_group('',$label,"<input type='text' class='input-block-level' name='$custom_fields[$i]' value='" . $$custom_fields[$i] . "'>");
		}	elseif(substr($custom_fields[$i],0,11) == "custom_text") {
			$label = substr($custom_fields[$i],12);
			echo tpl_form_control_group('',$label,"<textarea class='input-block-level' rows='6' name='$custom_fields[$i]'>" . $$custom_fields[$i] . "</textarea>");
		}	elseif(substr($custom_fields[$i],0,14) == "custom_wysiwyg") {
			$label = substr($custom_fields[$i],15);
			echo tpl_form_control_group('',$label,"<textarea class='mceEditor_small' name='$custom_fields[$i]'>" . $$custom_fields[$i] . "</textarea>");
		}		
	}

echo'</div>'; /* EOL tab custom fields */

}

echo"</div>"; // EOL fancytabs



//submit form to save data

echo '<div class="formfooter">';
echo '<input type="hidden" name="page_version" value="'.$page_version.'">';
echo '<div style="float:right;">'.$submit_button.' '.$previev_button.'</div>'. $delete_button;
echo '<div style="clear:both;"></div>';
echo '</div>';

echo '</form>';



?>