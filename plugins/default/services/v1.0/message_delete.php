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

$id   = input('id');
$guid = input('guid'); //user id
if(!com_is_active('OssnMessages')) {
		$params['OssnServices']->throwError('201', ossn_print('ossnservices:component:notfound'));
}
$message = ossn_get_message($id);

if($message && $message->message_from == $guid) {
		$message->message          = ''; //delete message data
		$message->data->is_deleted = true;
		if($message->save()) {
				$params['OssnServices']->successResponse(true);
		}
}
$params['OssnServices']->throwError('200', ossn_print('ossnservices:messagedeletefailed'));