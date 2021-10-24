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
$message = input('message');
$to      = input('to');
$from    = input('from');
if(!com_is_active('OssnMessages')) {
		$params['OssnServices']->throwError('201', ossn_print('ossnservices:component:notfound'));
}
if(trim(ossn_restore_new_lines($message)) == '') {
		$params['OssnServices']->throwError('106', ossn_print('ossnservices:messagecannotblank'));
}
if(empty($to) || empty($from)) {
		$params['OssnServices']->throwError('106', ossn_print('ossnservices:empty:field:one:more'));
}
$send = new OssnMessages;

$from = ossn_user_by_guid($from);
$to   = ossn_user_by_guid($to);
if(!$from || !$to){
	$params['OssnServices']->throwError('103', ossn_print('ossnservices:nouser'));	
}
if($id = $send->send($from->guid, $to->guid, $message)) {
		$item = $send->getMessage($id);
		
		$item->message_from = $params['OssnServices']->setUser($from, true);
		$item->message_to = $params['OssnServices']->setUser($to, true);
		
		$item->message = nl2br(ossn_restore_new_lines($item->message));		
		$params['OssnServices']->successResponse($item);
}
$params['OssnServices']->throwError('200', ossn_print('ossnservices:messagesendfailed'));