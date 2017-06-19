<?php // redcap_data_entry_form.php

	return function ($project_id){
		$path = $_SERVER['REQUEST_URI'];
		parse_str($_SERVER['QUERY_STRING'], $qs_params);
		$pid = $qs_params['pid'];
		$webroot_path = APP_PATH_WEBROOT ;
		// Load the hook only on project setup page.
		if(preg_match('/ProjectSetup\/index/', $path) == 1){?>
			<script type="text/javascript">
			var app_path_images = '<?php echo APP_PATH_IMAGES ?>';
			var webroot_path = '<?php echo $webroot_path ?>';
			var base_path = webroot_path.split('/redcap_v')[0];
			document.addEventListener('DOMContentLoaded', function(){
				var cpsHookHtml = '<div id="setupChklist-design" class="round chklist col-xs-12"><table cellspacing="0" width="100%">'+
									'<tbody><tr><td valign="top" style="width:70px;text-align:center;"><div>'+
									'<img id="img-design" src="'+app_path_images+'checkbox_gear.png">'+
									'</div><div id="lbl-modules" style="color:#999;">Optional</div></td>'+
									'<td valign="top" style="padding-left:30px;"><div class="chklisthdr">'+
									'<span>Custom Project Settings</span></div>'+
									'<div class="chklisttext">Add settings for REDCap extensions using '+ 
									'<a href="'+base_path+'/plugins/redcap_custom_project_settings/index.php?pid=<?php echo $pid; ?>"'+
									'style="text-decoration:underline;color:#800000;">Custom Project Settings</a>. '+
									'The extensions need to be installed separately. You will also need to know the attribute each extension expects to read and how to format the values the extension needs to configure itself. '+
									' </div></td></tr></tbody></table></div>';

				$("#setupChklist-modify_project").after(cpsHookHtml);
			});
				
			</script>
			<?php
		}
	}
  ?>