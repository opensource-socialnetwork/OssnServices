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
$guid = input('guid');
if(!$guid) {
		$params['OssnServices']->throwError('106', ossn_print('ossnservices:empty:field:one:more'));
}
if($guid) {
		$user = ossn_user_by_guid($guid);
}
if($user && !$user->isAdmin()) {
		if($user->deleteUser()){
			$params['OssnServices']->successResponse(array(
					'success' => true,
			));
		} else {
			$params['OssnServices']->throwError('200', ossn_print('admin:user:delete:error'));
		}
} else {
		$params['OssnServices']->throwError('103', ossn_print('ossnservices:nouser'));
}