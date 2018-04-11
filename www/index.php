<?php
	include('../include/init.php');

	#
	# get list of all pet species and their total sightings
	#

	$pets = array();

	$ret = db_fetch("SELECT * FROM pets_defs WHERE is_wild='Y'");
	foreach ($ret['rows'] as $row){

		$pets[$row['species_id']] = $row;
	}

	$ret = db_fetch("SELECT pet_id, SUM(count) AS total FROM pets_data GROUP BY pet_id");
	foreach ($ret['rows'] as $row){

		if (!is_array($pets[$row['pet_id']])) continue;

		$pets[$row['pet_id']]['total_seen'] = $row['total'];
	}

	$smarty->assign('pets', $pets);


	#
	# output
	#

	$smarty->display('page_index.txt');
