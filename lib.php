<?php

function local_deluser_aftertime_extend_settings_navigation(settings_navigation $nav){
	$url = new moodle_url('/local/deluser_aftertime/index.php');
	$pluginname = get_string('pluginname', 'local_deluser_aftertime');
	
	$node = $nav->get('root');
    if ($node) {
        $node = $node->get('reports');
		//$node = $node->get('roles');
    }
	//echo '<pre>'.print_r($node,true).'</pre>';
	//User policies
	if ($node) {
        $condrole = $node->add($pluginname, $url, navigation_node::TYPE_SETTING,
                                $pluginname, null);
        
    }
	
	
}

/*local_crontest_cron(){
	global $DB;	
	$DB->set_field('local_crontest', 'runtime', time(), ['id'=>'1']);
	
}*/