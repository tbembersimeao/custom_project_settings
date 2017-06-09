<?php


/**

    A plugin for editing custom project settings

**/

error_reporting(E_ALL);

//dirname(dirname(__FILE__)) . '/Config/init_project.php';
//require_once dirname(dirname(__FILE__)) . '/Config/init_project.php';

if (!file_exists('../../redcap_connect.php')) {
    $REDCAP_ROOT = "/var/www/redcap";
    require_once $REDCAP_ROOT . '/redcap_connect.php';
} else {
    require_once '../../redcap_connect.php';
}

require_once APP_PATH_DOCROOT . "ProjectGeneral/form_renderer_functions.php";
require_once "cps_lib.php";
// require_once (__DIR__.'/../deploy/plugins/redcap_custom_project_settings/cps.php');
// require_once (__DIR__.'/../deploy/plugins/redcap_custom_project_settings/cps_lib.php');

$debug = array();

// The $config array contains key-value attributes that control the rendering of the page:
//      filter          = logic to be applied to filter records
//      arm             = active arm number being displayed
//      num_per_page    = number of records per page
//      pagenum         = current page number
//      group_by        = form or event for headers in longitudinal table
//      excluded_forms  = csv list of forms to exclude from grid
//      ext_id  = The bookmark ID if saved to the database
//      vertical_header = 1/0 to twist header
//      record_label    = like a custom record label to add a text column to the dashboard..



// Load the query string and script URI
parse_str($_SERVER['QUERY_STRING'], $qs_params);
$scriptUri = $_SERVER['SCRIPT_URI'];
$parseUrl = parse_url($_SERVER['REQUEST_URI']);
$relativePath = $parseUrl['path'];
$debug['qs_params'] = $qs_params;
$debug['scriptUri'] = $scriptUri;
$debug['relativePath'] = $relativePath;

// Saved bookmarks use the 'settings' attribute in the query string.  If set, apply these values over the defaults
$settings = isset($qs_params['settings']) ? json_decode(urldecode($qs_params['settings']),true) : NULL;
$debug['settings'] = $settings;
if (!empty($settings)) $config = array_merge($config,$settings);
$debug['config_after_settings'] = $config;

// Get User Rights
global $user_rights;
$user_rights = REDCap::getUserRights(USERID);
$user_rights = $user_rights[USERID];

// Determien whether or not the current user can 'edit' the custom dashboard (requires reports rights)
$config['can_edit'] = (SUPER_USER || $user_rights['reports']);


// Fetch custom settings data and store it
$pid = $qs_params['pid'];
$cps = new cps_lib();
$cps_data = $cps->getDataByProjectId($pid);
//print_r(count($cps_data));

// This is the initial load (via GET) - so lets render the page
if (empty($_POST)) {
    // RENDER THE PAGE
    include APP_PATH_DOCROOT . 'ProjectGeneral/header.php';
?>

		<style type="text/css">
			.custom-container{
				padding: 0 15px;
			}
			.box{
				margin: 8px 0;
			    background-color: #fafafa;
			    border: 1px solid #ddd;
			    padding: 5px 10px;
			    font-size: 13px;
			    max-width: 700px;
			}
			.box-body table{
				width: 99%;
			}
			.box-header{
				padding: 10px;
				border-bottom: 1px solid #ddd;
				text-align: center;
				font-size: 1.2em;
				font-weight: bold;
			}
			.form-text{
				padding: 2px 5px;
    			border: 1px solid #c1c1c1;
    			width: 90%;
			}
			.form-textarea{
				padding: 2px 5px;
    			border: 1px solid #c1c1c1;
    			width: 95%;
			}
			.btn{
				padding: 0.2em 0.6em 0.3em;
				cursor: pointer;
				border: 1px solid #d3d3d3;
    			font-weight: normal;
    			color: #555;
    			font-size: 0.9em;
    			border-radius: 4px;
			}
			.btn-cancel, .btn-saveall{
				font-size: 1em;
				height: 30px;
			}
			.btn-holder{
				overflow: hidden;
				text-align: center;
			}
			.save-holder{
				float: left;
				width: 50%;
				text-align: center;
			}
			.delete-holder{
				text-align: center;
			}
			.footer-left{
				width: 50%;
				float: left;
			}
			.footer-right{
				width: 50%;
				float: right;
				text-align: right;
			}
			.box-footer{
				padding: 10px;
			    margin: 10px 0 5px;
			    border-top: 1px solid #ddd;
			    overflow: hidden;
			}
		</style>

		<div class="custom-container">
			<div class="box">
					<div class="box-header">
						CUSTOM PROJECT SETTINGS
					</div>
					<div class="box-body">
						<table id="customTable">
							<tbody>
								<tr>
									<th width="30%" style="padding:10px;text-align:center;font-size: 1.2em;">Attribute</th>
									<th width="50%" style="padding:10px;text-align:center;font-size: 1.2em;">Value</th>
									<th width="20%" style="padding:10px;text-align:center;font-size: 1.2em;">Action</th>
								</tr>
								<tr>
									<td>
										<input type="text" name="attribute" class="form-text" value=""/>
									</td>
									<td>
										<textarea rows="3" name="value" class="form-textarea" value=""></textarea>
										<input type="hidden" class="form-hidden" name="id" value=""/>
									</td>
									<td>
										<div class="btn-holder">
											<div class="delete-holder">
												<button type="button" class="btn btn-delete">Delete</button>
											</div>
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<input type="text" name="attribute" class="form-text" value=""/>
									</td>
									<td>
										<textarea rows="3" name="value" class="form-textarea" value=""></textarea>
										<input type="hidden" class="form-hidden" name="id" value=""/>
									</td>
									<td>
										<div class="btn-holder">
											<div class="delete-holder">
												<button type="button" class="btn btn-delete">Delete</button>
											</div>
										</div>
									</td>
								</tr>
								<tr class="last-row">
									<td>
										<button type="button" id="addRow">+</button>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="box-footer">
						<div class="footer-left">
							<button type="button" class="btn btn-saveall">Save Updates</button>
						</div>
					</div>
			</div>
		</div>
<?php
	
    
    // Page footer

    include APP_PATH_DOCROOT . 'ProjectGeneral/footer.php';
}
?>
<script type="text/javascript">
	document.addEventListener('DOMContentLoaded', function(){
		bindAdd(); 
		bindDelete(); 
		bindSaveUpdates(); 
		renderCpsData();
		
	});

	// Bind add row click event to add a new row to enter new configuration details(attribute and value).
	function bindAdd(){
		var addRow = document.getElementById('addRow');
		addRow.addEventListener('click', function(){
			addNewRow();	
		});
	}

	// Utils function to add a new row, containing attribute,value,delete, to the table.
	function addNewRow(){
		var row = document.createElement("tr");
		row.innerHTML = '<td><input type="text" name="attribute" class="form-text" value=""/></td>'+
					'<td><textarea rows="3" name="value" class="form-textarea"></textarea>'+
					'<input type="hidden" class="form-hidden" name="id" value=""/></td>'+
					'<td><div class="btn-holder"><div class="delete-holder">'+
					'<button type="button" class="btn btn-delete">Delete</button></div></div></td>';
		var lastRow = document.getElementsByClassName('last-row')[0];
		document.getElementById('customTable').getElementsByTagName('tbody')[0].insertBefore(row, lastRow);
		// Bind delete button click after adding a new row
		var deleteBtn = lastRow.previousSibling.getElementsByClassName('btn-delete')[0];
		deleteBtn.addEventListener('click', function(){
			var confirm = window.confirm('Are you sure you want to delete this configuration ?');
			if(confirm){
				$(this).closest('tr').remove();
			}
		});
	}

	// Bind delete row click event to delete a single row/configuration data.
	function bindDelete(){
		var deleteBtns = document.getElementsByClassName('btn-delete');
		for(var i=0;i<deleteBtns.length;i++){
			deleteBtns[i].addEventListener('click', function(e){
				var confirm = window.confirm('Are you sure you want to delete this configuration ?');
				if(confirm){
					$(this).closest('tr').remove();
				}
			});
		}
		
	}

	//Bind save updates click to save/update all configuration data from the cps form.
	function bindSaveUpdates(){
		$(".btn-saveall").click(function(){
			var cps_array=[];
			var trows = document.getElementById('customTable').getElementsByTagName('tr');
			for(var i=0;i<trows.length-2;i++){
				cps_array[i] = {};
				cps_array[i].project_id = <?php echo $pid ?>;
				cps_array[i].attribute = trows[i+1].getElementsByClassName('form-text')[0].value;
				cps_array[i].value = trows[i+1].getElementsByClassName('form-textarea')[0].value;
				cps_array[i].id = trows[i+1].getElementsByClassName('form-hidden')[0].value;
			}
			for(var i=0;i<cps_array.length;i++){
				if(!cps_array[i].attribute){
					alert("Please enter all required details.");
					return;
				}
			}
			$.ajax({
				type: "POST",
				url: "submit.php",
				data: {'arr' : JSON.stringify(cps_array)}
			}).done(function(msg){
				alert("Saved Successfully");
				<?php
					$cps_data = $cps->getDataByProjectId($pid);
				?>
				//renderCpsData();
			});
		});
	}

	// Render data to the form on page load.
	function renderCpsData(){
		/* 	Default form view has only two rows. 
			Add more rows if data to be rendered has more than two rows.
		*/
		var cpsDataLength = <?php echo count($cps_data); ?>;
		var trows = document.getElementById('customTable').getElementsByTagName('tr');
		if(trows-2 != cpsDataLength){
			for(var i=2;i<cpsDataLength;i++){
				addNewRow();
			}
		}

		/* Iterate over the json array and render data in the corresponding fields for each row.
		*/
		<?php
			$i = 1;
			foreach ($cps_data as $item) {
				$attr = $item['attribute'];
				$val = $item['value'];
				$project_id = $item['project_id'];
				$id = $item['id'];
		?>
			
			var i = <?php echo $i; ?>;

			trows[i].getElementsByClassName('form-text')[0].value = '<?php echo $attr; ?>';
			trows[i].getElementsByClassName('form-textarea')[0].value = '<?php echo $val; ?>';
			trows[i].getElementsByClassName('form-hidden')[0].value = '<?php echo $id; ?>';
		<?php
			$i++;
			}
		?>
	}

	
</script>
