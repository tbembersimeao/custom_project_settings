<?php
	require_once "cps_lib.php";
	require_once "cps.php";
	
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
	//print_r($cps_array);
  	
?>