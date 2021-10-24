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
$guid        = input('guid'); //for who?
$with        = input('to');
$offset      = input('offset');
$markallread = input('markallread');

ossn_set_input('offset_message_xhr_with', $offset);

if(!com_is_active('OssnMessages')) {
		$params['OssnServices']->throwError('201', ossn_print('ossnservices:component:notfound'));
}
$user = ossn_user_by_guid($guid);
$with = ossn_user_by_guid($with);
if(!$user || !$with) {
		$params['OssnServices']->throwError('200', ossn_print('ossnservices:nouser'));
}
$new   = new OssnMessages;
$all   = $new->getWith($user->guid, $with->guid);
$count = $new->getWith($user->guid, $with->guid, true);
$list  = false;

if(isset($markallread) && $markallread == 1) {
		$new->markViewed($with->guid, $user->guid);
}
if($all) {
		foreach($all as $item) {
				if($item->message_from == $user->guid) {
						$item->message_from = $params['OssnServices']->setUser($user, true);
						$item->message_to   = $params['OssnServices']->setUser($with, true);
				} else {
						$item->message_from = $params['OssnServices']->setUser($with, true);
						$item->message_to   = $params['OssnServices']->setUser($user, true);
				}
				$item->message = nl2br(ossn_restore_new_lines($item->message));
				$list[]        = $item;
		}
}
$params['OssnServices']->successResponse(array(
		'list' => $list,
		'withuser' => $params['OssnServices']->setUser($with, true),
		'count' => $count,
		'offset' => $offset
));