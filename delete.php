<?php
	require_once "cps_lib.php";
	require_once "cps.php";
	
	$cps_lib = new cps_lib();

	$id = $_POST['id'];

	// Delete data
	$cps_lib->deleteData($id);
?>