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
$to_block_guid = input('to_block_guid');

if(!$to_block_guid || !$guid) {
		$params['OssnServices']->throwError('106', ossn_print('ossnservices:empty:field:one:more'));
}
if($guid) {
		$user = ossn_user_by_guid($guid);
}
if($to_block_guid) {
		$to_block_user = ossn_user_by_guid($to_block_guid);
}
if($user && $to_block_user) {
		$block = new OssnBlock;
		if($block->addBlock($user->guid, $to_block_user->guid)){
			$params['OssnServices']->successResponse(array(
					'success' => true,
			));
		} else {
			$params['OssnServices']->throwError('200', ossn_print('user:block:error'));
		}
} else {
		$params['OssnServices']->throwError('103', ossn_print('ossnservices:nouser'));
}