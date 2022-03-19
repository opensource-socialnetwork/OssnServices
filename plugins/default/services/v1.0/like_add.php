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
if(!com_is_active('OssnLikes')) {
		$params['OssnServices']->throwError('201', ossn_print('ossnservices:component:notfound'));
}
$OssnLikes     = new OssnLikes();
$subject_guid  = input('subject_guid');
$type          = input('type');
$user_guid     = input('uguid');
$reaction_type = input('reaction_type', '', 'like');

$allowed_types = array(
		'post',
		'entity',
		'annotation',
);
if(!in_array($type, $allowed_types)) {
		$params['OssnServices']->throwError('200', ossn_print('ossnservices:like:failed:add'));
}
if(empty($subject_guid) || empty($type) || empty($user_guid)) {
		$params['OssnServices']->throwError('106', ossn_print('ossnservices:empty:field:one:more'));
}

if($OssnLikes->Like($subject_guid, $user_guid, $type, $reaction_type)) {
		$count      = $OssnLikes->CountLikes($subject_guid, $type);
		$last_three = false;
		if($OssnLikes->__likes_get_all) {
				foreach($OssnLikes->__likes_get_all as $item) {
						$last_three_icons[$item->subtype] = $item->subtype;
				}
				$last_three = new stdClass();
				$last_three = array_slice($last_three_icons, -3);
		}
		$params['OssnServices']->successResponse(array(
				'count'                => $count,
				'last_three_reactions' => $last_three,
		));
} else {
		$params['OssnServices']->throwError('200', ossn_print('ossnservices:like:failed:add'));
}