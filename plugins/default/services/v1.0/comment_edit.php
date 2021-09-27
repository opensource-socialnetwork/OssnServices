<?php
/**
 * Open Source Social Network
 *
 * @package   (openteknik.com).ossn
 * @author    OSSN Core Team <info@openteknik.com>
 * @copyright (C) OpenTeknik LLC
 * @license   Open Source Social Network License (OSSN LICENSE)  http://www.opensource-socialnetwork.org/licence
 * @link      https://www.opensource-socialnetwork.org/
 */
$guid    = input('guid');
$text    = input('comment');
$comment = ossn_get_annotation($guid);

$services = $params['OssnServices'];
if($comment && (strlen($text) || $comment->{'file:comment:photo'})) {
		$comment->data	= new stdClass;
		if($comment->type == 'comments:entity') {
				$comment->data->{'comments:entity'} = $text;
		} elseif($comment->type == 'comments:object') {
				$comment->data->{'comments:object'} = $text;
		} elseif($comment->type == 'comments:post') {
				$comment->data->{'comments:post'} = $text;
		}
		if($comment->save()) {
				$params               = array();
				$params['text']       = $text;
				$params['annotation'] = $comment;
				ossn_trigger_callback('comment', 'edited', $params);
		
				$services->successResponse(array(
							'success' => ossn_print('comment:edit:success'),										   
				));
				return;
		}
}
$params['OssnServices']->throwError('200', ossn_print('comment:edit:failed'));
