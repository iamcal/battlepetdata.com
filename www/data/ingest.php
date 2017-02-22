<?php
	include('../include/init.php');

	ingest('adjectivenoun.lua');
	ingest('iamcal.lua');


	function ingest($filename){

		$data = file_get_contents($filename);

		$data = str_replace('{', 'array(', $data);
		$data = str_replace('}', ')', $data);
		$data = str_replace('=', '=>', $data);
		$data = str_replace('[', '', $data);
		$data = str_replace(']', '', $data);
		$data = str_replace('BattleDexDB =>', '$BattleDexDB =', $data);

		eval($data.';');

		$cnt = 0;

		$filename_enc = AddSlashes($filename);
		db_write("DELETE FROm pets WHERE source='{$filename_enc}'");

		foreach ($BattleDexDB['pets'] as $k => $v){

			foreach ($v as $k2 => $v2){

				list($a,$b,$c) = explode('_', $k2);

				db_insert('pets', array(
					'source'	=> AddSlashes($filename),
					'pet_id'	=> intval($k),
					'battle_pet_id'	=> intval($a),
					'level'		=> intval($b),
					'quality'	=> intval($c),
					'count'		=> intval($v2),
				));

				$cnt++;
			}
		}

		echo "$filename - $cnt<br>\n";
	}