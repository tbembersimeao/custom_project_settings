<?php
/**
 * @file
 * Provides pages callbacks for Custom Project Settings project.
 */

/**
 * Page callback for the Custom Project Settings form.
 */
function custom_project_settings_page() {
    extract($GLOBALS);

    require_once APP_PATH_DOCROOT . "ProjectGeneral/form_renderer_functions.php";
    require_once "cps_lib.inc";
    // require_once (__DIR__.'/../deploy/plugins/custom_project_settings/cps.php');
    // require_once (__DIR__.'/../deploy/plugins/custom_project_settings/cps_lib.php');

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
    //print_r(($cps_data));

    // This is the initial load (via GET) - so lets render the page
    if (empty($_POST)) {
        // RENDER THE PAGE
        include APP_PATH_DOCROOT . 'ProjectGeneral/header.php';

?>
<style type="text/css">
    .custom-container, .help-text{
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
        resize: vertical;
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
    .help-text p{
        font-size: 11px;
    }
    .help-text p span{
        font-weight: bold;
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
                            <th width="30%" style="padding:10px;text-align:center;font-size: 1.1em;">Attribute</th>
                            <th width="50%" style="padding:10px;text-align:center;font-size: 1.1em;">Value</th>
                            <th width="20%" style="padding:10px;text-align:center;font-size: 1.1em;">Action</th>
                        </tr>
                        <tr>
                            <td>
                                <input type="text" name="attribute" class="form-text" value="" placeholder="Unique Extension Name"/>
                            </td>
                            <td>
                                <textarea rows="3" name="value" class="form-textarea" value="" placeholder="Configuration data for the extension"></textarea>
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
<div class="help-text">
    <p><span>NOTE: </span>Provide attribute-value pairs to configure your REDCap extensions.</p>
    <p>The attribute is the name of the key by which your extension will find its configuration data. The value is the data the extension needs to configure itself. Typically this is a JSON string. Note that neither the attribute nor the value are checked by this page.</p>
    <p><span>NOTE: </span>This page does not install REDCap extensions. You will need to install extensions separately.</p>
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
        row.innerHTML = '<td><input type="text" name="attribute" class="form-text" value="" placeholder="Unique Extension Name"/></td>'+
                    '<td><textarea rows="3" name="value" class="form-textarea" placeholder="Configuration data for the extension"></textarea>'+
                    '<input type="hidden" class="form-hidden" name="id" value=""/></td>'+
                    '<td><div class="btn-holder"><div class="delete-holder">'+
                    '<button type="button" class="btn btn-delete">Delete</button></div></div></td>';
        var lastRow = document.getElementsByClassName('last-row')[0];
        document.getElementById('customTable').getElementsByTagName('tbody')[0].insertBefore(row, lastRow);
        // Bind delete button click after adding a new row
        var deleteBtn = lastRow.previousSibling.getElementsByClassName('btn-delete')[0];
        deleteBtn.addEventListener('click', function(){
            var thisBtn = $(this);
            deleteThisRow(thisBtn);
        });
    }

    function deleteThisRow(thisBtn){
        var confirm = window.confirm('Are you sure you want to delete this configuration ?');
        var id = thisBtn.closest('tr').find('.form-hidden').val();
        if(confirm){
            if(id != '' && id != null){
                $.ajax({
                    type: 'POST',
                    url: '<?php print APP_PATH_WEBROOT_PARENT; ?>plugins/custom_project_settings/delete.php?pid=<?php echo $pid; ?>',
                    data: {'id': id}
                }).done(function(msg){
                    thisBtn.closest('tr').remove();
                    //alert('Deleted successfully!');
                });
            } else{
                thisBtn.closest('tr').remove();
            }
        }
    }

    // Bind delete row click event to delete a single row/configuration data.
    function bindDelete(){
        var deleteBtns = document.getElementsByClassName('btn-delete');
        for(var i=0;i<deleteBtns.length;i++){
            deleteBtns[i].addEventListener('click', function(){
                var thisBtn = $(this);
                deleteThisRow(thisBtn);

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
                var val1 = trows[i+1].getElementsByClassName('form-textarea')[0].value;
                var jsonstring = "";
                try {
                    jsonstring = JSON.stringify(JSON.parse(val1));
                } catch(e) {
                    var format = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]+/;
                    if(format.test(val1)){
                      continue;
                    }
                    jsonstring = val1;
                }
                cps_array[i].value = jsonstring;
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
                url: "<?php echo APP_PATH_WEBROOT_PARENT; ?>plugins/custom_project_settings/submit.php?pid=<?php echo $pid; ?>",
                dataType: 'json',
                data: {'arr' : JSON.stringify(cps_array)}
            }).done(function(response){
                alert('Saved successfully!');
                var parsedRes = response;
                var trows = document.getElementById('customTable').getElementsByTagName('tr');
                for(var i=0;i<parsedRes.length;i++){
                    var $attributeVal = parsedRes[i].value;
                    var obj = "";
                    var pretty = "";
                    try {
                        obj = JSON.parse($attributeVal);
                        pretty = JSON.stringify(obj, undefined, 4);
                        // console.log(typeof obj);
                    } catch (e) {
                        pretty = $attributeVal;
                    }
                    trows[i+1].getElementsByClassName('form-text')[0].value = parsedRes[i].attribute;
                    trows[i+1].getElementsByClassName('form-textarea')[0].value = pretty;
                    trows[i+1].getElementsByClassName('form-hidden')[0].value = parsedRes[i].id;
                }
                /* Temporary fix: Reload page to render latest data.
                */
                //window.location.reload();
            });
        });
    }

    // Render data to the form on page load.
    function renderCpsData(){
        /*  Default form view has only two rows.
            Add more rows if data to be rendered has more than two rows.
        */
        <?php $cps_data = $cps->getDataByProjectId($pid); ?>
        var cpsDataLength = <?php echo count($cps_data); ?>;
        var trows = document.getElementById('customTable').getElementsByTagName('tr');
        if(trows.length-2 != cpsDataLength){
            for(var i=1;i<cpsDataLength;i++){
                addNewRow();
            }
        }

        /* Iterate over the json array and render data in the corresponding fields for each row.
        */
        <?php
            $i = 1;
            //print_r($cps_data);
            foreach ($cps_data as $item) {
                $attr = $item->attribute;
                $val = $item->value;
                $project_id = $item->project_id;
                $id = $item->id;
        ?>

            var i = <?php echo $i; ?>;
            var $attributeVal = '<?php echo $val; ?>';
            var obj = "";
            var pretty = "";
            try {
                obj = JSON.parse($attributeVal);
                pretty = JSON.stringify(obj, undefined, 4);
                // console.log(typeof obj);
            } catch (e) {
                pretty = $attributeVal;
            }

            trows[i].getElementsByClassName('form-text')[0].value = '<?php echo $attr; ?>';
            trows[i].getElementsByClassName('form-textarea')[0].value = pretty;
            trows[i].getElementsByClassName('form-hidden')[0].value = '<?php echo $id; ?>';
        <?php
            $i++;
            }
        ?>
    }
</script>
<?php
}

/**
 * Page callback for the Custom Project Settings submit handler page.
 */
function custom_project_settings_submit() {
    require_once "cps_lib.inc";
    require_once "cps.inc";

    $cps_lib = new cps_lib();

    // Converts a JSON encoed string into a PHP variable.
    $formData = html_entity_decode($_POST['arr']);
    $formattedData = json_decode($formData);

    // Create an array of (cps)objects.
    $i = 0;
    $cps_array = array();
    foreach ($formattedData as $item){
        $cps = new cps();
        $cps->attribute = $item->attribute;
        $cps->value = $item->value;
        $cps->project_id = $item->project_id;
        $cps->created_by = 'admin';
        $cps->id = $item->id;
        $cps_array[$i] = $cps;
        $i++;
    }

    // Save data.
    $cps_lib->save($cps_array);
    $pid = reset($cps_array)->project_id;
    $cps_data = $cps_lib->getDataByProjectId($pid);
    print_r(json_encode($cps_data));
}

/**
 * Page callback for the Custom Project Settings delete handler page.
 */
function custom_project_settings_delete() {
    require_once "cps_lib.inc";
    require_once "cps.inc";

    $cps_lib = new cps_lib();

    $id = $_POST['id'];

    // Delete data
    $cps_lib->deleteData($id);
}
?>
