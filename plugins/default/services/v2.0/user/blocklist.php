<?php
/**
 * Open Source Social Network
 *
 * @package   Open Source Social Network
 * @author    Open Social Website Core Team <info@openteknik.com>
 * @copyright © OPENTEKNIK LLC
 * @license   Open Source Social Network License (OSSN LICENSE)  http://www.opensource-socialnetwork.org/licence
 * @link      https://www.opensource-socialnetwork.org/
 */
if(!com_is_active('OssnBlock')) {
		$params['OssnServices']->throwError('201', ossn_print('ossnservices:component:notfound'));
}
$guid = input('guid');

if(!$guid) {
		$params['OssnServices']->throwError('106', ossn_print('ossnservices:empty:field:one:more'));
}
if($guid) {
		$user = ossn_user_by_guid($guid);
}
if($user) {
		$list = ossn_get_relationships(array(
					'from' => $guid,
					'type' => 'userblock',
					'page_limit' => false,
		));
		$lists = false;
		if($list){
				$lists = array();
				foreach($list as $item){
						$lists[]  =  $params['OssnServices']->setUser(ossn_user_by_guid($item->relation_to));	
				}
		}	
		$params['OssnServices']->successResponse(array(
				'list' => $lists,
		));
} else {
		$params['OssnServices']->throwError('103', ossn_print('ossnservices:nouser'));
}