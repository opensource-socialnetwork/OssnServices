<?php
/**
 * Open Source Social Network
 *
 * @package   Open Source Social Network
 * @author    Haydar Alkaduhimi <haydar@developing4all.com>
 * @copyright © Developing 4 All
 * @license   GPL
 * @link      https://www.developing4all.com/
 */
$usera = input('user_a');
$userb = input('user_b');

if(!$usera || !$userb) {
		$params['OssnServices']->throwError('106', ossn_print('ossnservices:empty:field:one:more'));
}
if($usera) {
		$usera = ossn_user_by_guid($usera);
}
if($userb) {
		$userb = ossn_user_by_guid($userb);
}

if($usera && $userb) {
    $response                   = array();
    $response['is_friend']      = false;
    $response['request_exists'] = false;
    if($usera->sendRequest($usera->guid, $userb->guid)) {
            $response['success']        = true;
            $response['is_friend'] = true;
    } else {
            $response['success'] = false;
            if($usera->isFriend($usera->guid, $userb->guid)) {
                $response['success']   = true;
                $response['is_friend'] = true;
            }
            if(ossn_user()->requestExists($usera->guid, $userb->guid)) {
                    $response['request_exists'] = true;
            }
    }
    $params['OssnServices']->successResponse($response);
} else {
    $params['OssnServices']->throwError('103', ossn_print('ossnservices:nouser'));
}