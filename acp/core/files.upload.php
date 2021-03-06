<?php

//prohibit unauthorized access
require("core/access.php");


$prefs = get_preferences();
$form_img_action = "core/files.upload-script.php";
$files_d = FILES_FOLDER;
$form_files_action = "core/files.upload-script.php?d=$files_d";

?>

<div class="row-fluid">
<div class="span6">
<fieldset>
	<legend><?php echo $lang['upload_img_legend']; ?></legend>
	<form action="core/files.upload-script.php" id="myDropzone" class="dropzone">
		<input type="hidden" name="w" value="<?php echo $prefs[prefs_maximagewidth]; ?>" />
		<input type="hidden" name="h" value="<?php echo $prefs[prefs_maximageheight]; ?>" />
		<input type="hidden" name="fz" value="<?php echo $prefs[prefs_maxfilesize]; ?>" />
		<input type="hidden" name="d" value="<?php echo $img_path; ?>" />
		<input type="hidden" name="upload_type" value="images" />
		<div class="fallback">
			<input name="file" type="file" multiple />
		</div>
	</form>
	<hr>
	<?php echo $prefs[prefs_imagesuffix] ?>
</fieldset>
</div>
<div class="span6">
<fieldset>
	<legend><?php echo $lang['upload_files_legend']; ?></legend>
		<form action="core/files.upload-script.php" id="my-dropzone2" class="dropzone">
			<input type="hidden" name="d" value="<?php echo $files_d; ?>" />
			<input type="hidden" name="upload_type" value="files" />
			<div class="fallback">
				<input name="file" type="file" multiple />
			</div>
		</form>
		<hr>
		<?php echo $prefs[prefs_filesuffix] ?>
</fieldset>
	</div>
</div>