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
$guid = input('guid'); //for who? 
if(!com_is_active('OssnMessages')) {
		$params['OssnServices']->throwError('201', ossn_print('ossnservices:component:notfound'));
}
$offset = input('offset', '', 1);
ossn_set_input('offset_message_xhr_recent', $offset);

$user = ossn_user_by_guid($guid);
if(!$user) {
		$params['OssnServices']->throwError('200', ossn_print('ossnservices:nouser'));
}
$new  = new OssnMessages;
$all  = $new->recentChat($user->guid);
$count  = $new->recentChat($user->guid, true);
$list = false;
if($all) {
		foreach($all as $item) {
				if($item->message_from == $user->guid) {
						$item->message_from = $params['OssnServices']->setUser($user, true);
						$item->message_to   = $params['OssnServices']->setUser(ossn_user_by_guid($item->message_to), true);
				} else {
						$item->message_from = $params['OssnServices']->setUser(ossn_user_by_guid($item->message_from), true);
						$item->message_to   = $params['OssnServices']->setUser($user, true);
				}
				$list[] = $item;
		}
}
$params['OssnServices']->successResponse(array(
		'list' => $list,		
		'offset' => $offset,
		'count' => $count,
));