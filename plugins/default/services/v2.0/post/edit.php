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

$post_guid = input('post_guid');
$guid      = input('uguid'); //user guid
$newtext   = input('text');

if($guid) {
		$user = ossn_user_by_guid($guid);
}
if($user && com_is_active('OssnWall')) {
		$wall = new OssnWall();

		$post = $wall->GetPost($post_guid);
		if(!$post) {
				$params['OssnServices']->throwError('200', array(
						'status'  => 'failed',
						'message' => ossn_print('ossn:wall:post:save:error'),
				));
		}
		if(!$newtext && !isset($object->{'file:wallphoto'})) {
				$params['OssnServices']->throwError('200', array(
						'status'  => 'failed',
						'message' => ossn_print('ossn:wall:post:save:error'),
				));
		}
		if($post->poster_guid == $user->guid) {
				$json = $post->description;

				$data         = json_decode($json, true);
				$data['post'] = $newtext;
				$data         = json_encode($data, JSON_UNESCAPED_UNICODE);

				$post->description = $data;

				if($post->save()) {
						ossn_trigger_callback('wall', 'post:edited', array(
								'text'   => $newtext,
								'object' => $post,
						));

						$params['OssnServices']->successResponse(array(
								'status'  => 'success',
								'message' => ossn_print('ossn:wall:post:saved'),
						));
				} else {
						$params['OssnServices']->throwError('200', array(
								'status'  => 'failed',
								'message' => ossn_print('ossn:wall:post:save:error'),
						));
				}
		} else {
				$params['OssnServices']->throwError('200', ossn_print('ossnservices:invalidowner'));
		}
} else {
		$params['OssnServices']->throwError('103', ossn_print('ossnservices:nouser'));
}