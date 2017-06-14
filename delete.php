<?php
	require_once "cps_lib.php";
	require_once "cps.php";
	if (!file_exists('../../redcap_connect.php')) {
		$REDCAP_ROOT = "/var/www/redcap";
		require_once $REDCAP_ROOT . '/redcap_connect.php';
	} else {
		require_once '../../redcap_connect.php';
	}
	global $conn;
	$cps_lib = new cps_lib($conn);

	$id = $_POST['id'];

	// Delete data
	$cps_lib->deleteData($id);
?>