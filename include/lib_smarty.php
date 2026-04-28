<?php

	$GLOBALS['timings']['smarty_comp_count']	= 0;
	$GLOBALS['timings']['smarty_comp_time']	= 0;

	$GLOBALS['smarty'] = new Smarty\Smarty();

	$GLOBALS['smarty']->setTemplateDir($GLOBALS['cfg']['smarty_template_dir']);
	$GLOBALS['smarty']->setCompileDir($GLOBALS['cfg']['smarty_compile_dir']);
	$GLOBALS['smarty']->compile_check = $GLOBALS['cfg']['smarty_compile'];
	$GLOBALS['smarty']->force_compile = $GLOBALS['cfg']['smarty_force_compile'];

	$GLOBALS['smarty']->assign('cfg', $GLOBALS['cfg']);

	$GLOBALS['smarty']->registerPlugin('modifier', 'intval', 'intval');
	$GLOBALS['smarty']->registerPlugin('modifier', 'header', 'header');

	#######################################################################################

	function smarty_timings(){

		$GLOBALS['timings']['smarty_timings_out'] = microtime_ms();

		echo "<div class=\"admin-timings-wrapper\">\n";
		echo "<table class=\"admin-timings\">\n";

		# we add this one last so it goes at the bottom of the list
		$GLOBALS['timing_keys']['smarty_comp'] = 'Templates Compiled';

		foreach ($GLOBALS['timing_keys'] as $k => $v){
			$c = intval($GLOBALS['timings']["{$k}_count"] ?? 0);
			$t = intval($GLOBALS['timings']["{$k}_time"] ?? 0);
			echo "<tr><td>$v</td><td class=\"tar\">$c</td><td class=\"tar\">$t ms</td></tr>\n";
		}

		$t_init_end = $GLOBALS['timings']['init_end'] ?? 0;
		$t_exec_start = $GLOBALS['timings']['execution_start'] ?? 0;
		$t_smarty_start = $GLOBALS['timings']['smarty_start_output'] ?? 0;
		$t_smarty_out = $GLOBALS['timings']['smarty_timings_out'] ?? 0;

		$map2 = array(
			array("Startup &amp; Libraries", $t_init_end - $t_exec_start),
			array("Page Execution", $t_smarty_start - $t_init_end),
			array("Smarty Output", $t_smarty_out - $t_smarty_start),
			array("<b>Total</b>", $t_smarty_out - $t_exec_start),
		);

		foreach ($map2 as $a){
			echo "<tr><td colspan=\"2\">$a[0]</td><td class=\"tar\">$a[1] ms</td></tr>\n";
		}

		echo "</table>\n";
		echo "</div>\n";
	}

	$GLOBALS['smarty']->registerPlugin('function', 'timings', 'smarty_timings');

	#######################################################################################
