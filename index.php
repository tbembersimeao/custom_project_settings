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
require_once (__DIR__.'/../deploy/plugins/redcap_custom_project_settings/cps.php');
require_once (__DIR__.'/../deploy/plugins/redcap_custom_project_settings/cps_lib.php');

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

// This is the initial load (via GET) - so lets render the page
if (empty($_POST)) {
    // RENDER THE PAGE
    include APP_PATH_DOCROOT . 'ProjectGeneral/header.php';

    echo "included file". '<br>';
    $t2 = new cps();
    $t2->id = 1;
    echo $t2->id . "id val" . "<br>";
    $t2->attribute = "link";
    echo $t2->attribute . "<br>";

    $output = shell_exec('whoami');
    echo "<pre>$output</pre>";

    $table = new cps_lib();
?>
    <b>'Hello world!'</b>
    <?php
    
    // Page footer
    include APP_PATH_DOCROOT . 'ProjectGeneral/footer.php';
}

?>