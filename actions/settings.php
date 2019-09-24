<?php
/**
 * Open Source Social Network
 *
 * @package   Open Source Social Network
 * @author    Open Social Website Core Team <info@softlab24.com>
 * @copyright SOFTLAB24 LIMITED
 * @license   Open Source Social Network License (OSSN LICENSE)  http://www.opensource-socialnetwork.org/licence
 * @link      https://www.opensource-socialnetwork.org/
 */
$confirm = input('confirm');
$component = new OssnComponents;
if($confirm == 1){
 	$args = array(
			   'apikey' => (new \Ossn\Component\OssnServices())->genKey(),
	);
 	if($component->setSettings('OssnServices', $args)){	
				ossn_trigger_message(ossn_print('ossnservices:generated'));
				redirect(REF);
	}
}
redirect(REF);
