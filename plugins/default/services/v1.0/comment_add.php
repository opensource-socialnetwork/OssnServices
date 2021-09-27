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
if(!com_is_active('OssnComments')) {
		$params['OssnServices']->throwError('201', ossn_print('ossnservices:component:notfound'));
}
$OssnComment  = new OssnComments;
$subject_guid = input('subject_guid');
$comment      = input('comment');
$type         = input('type', false, 'post');
$user_guid    = input('uguid');

if(empty($subject_guid) || empty($type) || empty($user_guid)) {
		$params['OssnServices']->throwError('106', ossn_print('ossnservices:empty:field:one:more'));
}
if($type == 'post') {
		$object = ossn_get_object($subject_guid);
		if(!$object || $object && $object->subtype !== 'wall') {
				$params['OssnServices']->throwError('200', ossn_print('ossnservices:comment:failed:add'));
		}
}
if($type == 'entity') {
		$entity = ossn_get_entity($subject_guid);
		if(!$entity) {
				$params['OssnServices']->throwError('200', ossn_print('ossnservices:comment:failed:add'));
		}
}
if(isset($_FILES['image_file']) && !empty($_FILES['image_file'])){
	$OssnComment->comment_image = 'yes';
}	
if($guid = $OssnComment->PostComment($subject_guid, $user_guid, $comment, $type)) {
		$file          = new OssnFile;
		$file->type    = 'annotation';
		$file->subtype = 'comment:photo';
		$file->setFile('image_file');
		$file->setPath('comment/photo/');
		$file->setExtension(array(
				'jpg',
				'png',
				'jpeg',
				'gif'
		));
		$file->owner_guid = $guid;
		$file->addFile(); 
		$params['OssnServices']->successResponse(array(
				'comment' => ossn_get_comment($guid),
				'user' => $params['OssnServices']->setUser(ossn_user_by_guid($user_guid))
		));
} else {
		$params['OssnServices']->throwError('200', ossn_print('ossnservices:comment:failed:add'));
}
