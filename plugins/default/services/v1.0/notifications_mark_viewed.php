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

$guid = input('notification_guid');

if($guid && com_is_active('OssnNotifications')) {
		$notification = new OssnNotifications;
		$notification->guid = $guid;
		if($notification->setViewed($guid)) {
				$litem  = $notification->getbyGUID($guid);
				$option = array(
						'notification' => $litem,
						'poster' => false,
						'entity' => false,
						'post' => false
				);
				$poster = ossn_user_by_guid($litem->poster_guid);
				$owner  = ossn_user_by_guid($litem->owner_guid);
				if($poster) {
						$option['poster'] = array(
								'guid' => $poster->guid,
								'fullname' => $poster->fullname,
								'icon' => $poster->iconURL()->small
						);
				}
				if($litem->type == 'like:annotation') {
						$annotation = ossn_get_annotation($litem->item_guid);
						if($annotation) {
								if($annotation->type == 'comments:entity') {
										$entity           = ossn_get_entity($annotation->subject_guid);
										$option['entity'] = $entity;
								}
								if($annotation->type == 'comments:post') {
										$post = ossn_get_object($annotation->subject_guid);
										if($post) {
												$option['post']['guid']  = $post->guid;
												$option['post']['post_type']  = $post->type;
												$option['post']['owner_guid'] = $post->owner_guid;
												if($post->type == 'group') {
														$group = ossn_get_group_by_guid($post->owner_guid);
														if($group) {
																$option['group']['guid']     = $group->guid;
																$option['group']['title']    = $group->title;
																$option['group']['ismember'] = $group->isMember(NULL, $owner_guid);
														}
												}
										}
								}
						}
				}
				if($litem->type == "group:joinrequest") {
						$group = ossn_get_group_by_guid($litem->subject_guid);
						if($group) {
								$option['group']['guid']     = $group->guid;
								$option['group']['title']    = $group->title;
								$option['group']['ismember'] = $group->isMember(NULL, $owner_guid);
						}
				}
				$hook	 = ossn_call_hook('ossn:services', 'notifications_mark_viewed:item', false, $option);
				$params['OssnServices']->successResponse($hook);
		}
}
$params['OssnServices']->throwError('200', ossn_print('ossnservices:notification:cannotmark'));