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
$guid = input('guid'); //user who is strying to delete comment
$id   = input('id');
if(!com_is_active('OssnComments') || !com_is_active('OssnLikes')) {
		$params['OssnServices']->throwError('201', ossn_print('ossnservices:component:notfound'));
}
$comments = new OssnComments;
$comment  = $comments->GetComment($id);
if($comment) {
		if($comment->owner_guid == $guid) {
				if($comment->deleteComment($id)) {
						$params['OssnServices']->successResponse(true);
				}
		}
}
$params['OssnServices']->throwError('200', ossn_print('ossnservices:cannotdelete:comment'));