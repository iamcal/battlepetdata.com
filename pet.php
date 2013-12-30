<?php
	include('include/init.php');

	#
	# get pet
	#

	$url_enc = AddSlashes($_GET['id']);
	$pet = db_single(db_fetch("SELECT * FROM warcraftpets.pets WHERE url='{$url_enc}'"));

	if (!$pet['id']) error_404();

	$smarty->assign('pet', $pet);


	#
	# get stats
	#

	$stats = array(
		'total_seen' => 0,
		'qualities' => array(),
		'qualities_by_level' => array(),
	);

	$ret = db_fetch("SELECT * FROM pets WHERE pet_id={$pet['species_id']}");
	foreach ($ret['rows'] as $row){

		#$stats['levels'][$row['level']] = 
		#dumper($row);

		$stats['total_seen'] += $row['count'];
		$stats['qualities'][$row['quality']] += $row['count'];
		$stats['qualities_by_level'][$row['level']][$row['quality']] += $row['count'];
		$stats['levels'][$row['level']] += $row['count'];
		$stats['levels_by_primary'][$row['battle_pet_id']][$row['level']] += $row['count'];

		$pet_ids[$row['battle_pet_id']] = 1;
	}

	ksort($stats['qualities_by_level']);
	ksort($stats['levels']);


	#
	# find other secondaries
	#

	$rstats = array(
		'all' => array(),
	);

	$ret = db_fetch("SELECT * FROM pets WHERE battle_pet_id={$pet['species_id']}");
	foreach ($ret['rows'] as $row){

		$rstats['all'][$row['pet_id']] += $row['count'];
		$rstats['level'][$row['pet_id']][$row['level']] += $row['count'];

		$pet_ids[$row['pet_id']] = 1;
	}

	arsort($rstats['all']);


	#
	# get related pet names
	#

	$pets = array();

	$ids = implode(',', array_keys($pet_ids));
	$ret = db_fetch("SELECT * FROM warcraftpets.pets WHERE species_id IN ($ids)");
	foreach ($ret['rows'] as $row){

		$pets[$row['species_id']] = $row;
	}


	#
	# crunch stats for output
	#

	$out = array();

	$smarty->assign_by_ref('stats', $out);

	$out['total_seen'] = $stats['total_seen'];


	#
	# qualities
	#

	$out['qual_all'] = local_prep_quals($stats['qualities']);

	$out['qual_level'] = array();
	foreach ($stats['qualities_by_level'] as $level => $quals){
		$data = local_prep_quals($quals);
		$data['level'] = $level;
		$out['qual_level'][] = $data;
	}

	function local_prep_quals($a){

		$out = array(
			'num_1' => intval($a[1]),
			'num_2' => intval($a[2]),
			'num_3' => intval($a[3]),
			'num_4' => intval($a[4]),
		);

		$out['num_t'] = $out['num_1']  + $out['num_2'] + $out['num_3'] + $out['num_4'];

		$out['per_1'] = round(100 * $out['num_1'] / $out['num_t']);
		$out['per_2'] = round(100 * $out['num_2'] / $out['num_t']);
		$out['per_3'] = round(100 * $out['num_3'] / $out['num_t']);
		$out['per_4'] = round(100 * $out['num_4'] / $out['num_t']);

		$out['frac_1'] = 100 * $out['num_1'] / $out['num_t'];
		$out['frac_2'] = 100 * $out['num_2'] / $out['num_t'];
		$out['frac_3'] = 100 * $out['num_3'] / $out['num_t'];
		$out['frac_4'] = 100 * $out['num_4'] / $out['num_t'];

		return $out;
	}


	#
	# levels
	#

	$out['levels_all'] = local_prep_levels($stats['levels']);

	$out['levels_primary'] = array();
	foreach ($stats['levels_by_primary'] as $primary_id => $levels){
		$primary = $primary_id ? $pets[$primary_id] : $pet;

		$data = local_prep_levels($levels);
		$data['pet'] = $primary;
		$data['is_main'] = !$primary_id;

		$out['levels_primary'][] = $data;
	}

	function local_prep_levels($data){
		ksort($data);
		$out = array();
		$out['total'] = 0;
		$out['max'] = 0;
		foreach ($data as $v){
			$out['total'] += intval($v);
			$out['max'] = max($out['max'], intval($v));
		}
		$out['levels'] = array();
		foreach ($data as $k => $v){
			$out['levels'][] = array(
				'level'	=> $k,
				'num'	=> intval($v),
				'per'	=> round(100 * $v / $out['total']),
				'frac'	=> 100 * $v / $out['total'],
				'w'	=> 300 * $v / $out['max'],
			);
		}
		return $out;
	}


	#
	# Secondaries
	#

	$out['seconds'] = array();
	foreach ($rstats['all'] as $k => $v){
		$spet = $pets[$k];

		$spet['num'] = $v;
		$spet['levels'] = array();

		ksort($rstats['level'][$k]);

		$spet['simple_levels'] = implode(', ', array_keys($rstats['level'][$k]));

		foreach ($rstats['level'][$k] as $lvl => $num){
			$spet['levels'] = array(
				'level' => $lvl,
				'num' => $num,
			);
		}

		$out['seconds'][] = $spet;
	}


	#
	# output
	#

	$smarty->display('page_pet.txt');
