<?php
/**
 * Open Source Social Network
 *
 * @package   Open Source Social Network
 * @author    Open Social Website Core Team <info@softlab24.com>
 * @copyright © SOFTLAB24 LIMITED
 * @license   Open Source Social Network License (OSSN LICENSE)  http://www.opensource-socialnetwork.org/licence
 * @link      https://www.opensource-socialnetwork.org/
 */
$owner_guid = input('owner_guid');
$types      = input('types');
if($owner_guid) {
		$owner = ossn_user_by_guid($owner_guid);
}
if($owner && com_is_active('OssnNotifications')) {
		$notifications = new \Ossn\Component\Notifications;
		$options       = array(
				'owner_guid' => $owner_guid,
				'order_by' => 'n.guid DESC'
		);
		if(!empty($types)) {
				$json = json_decode(html_entity_decode($types), true);
				foreach($json as $item) {
						$typel[] = "'{$item}'";
				}
				$types_only        = implode(',', $typel);
				$options['wheres'] = "n.type IN ({$types_only})";
		}
		$list  = $notifications->searchNotifications($options);
		$listp = false;
		if($list) {
				foreach($list as $litem) {
						$option = array(
								'notification' => $litem,
								'poster' => false,
								'entity' => false,
								'post' => false,
						);
						$poster = ossn_user_by_guid($litem->poster_guid);
						$owner  = ossn_user_by_guid($litem->owner_guid);
						if($poster) {
								$option['poster'] = array(
										'guid' => $poster->guid,
										'fullname' => $poster->fullname,
										'icon' => $poster->iconURL()->small,						  
								);
						}
						if($litem->type == 'like:annotation') {
								$annotation = ossn_get_annotation($litem->item_guid);
								if($annotation) {
										if($annotation->type == 'comments:entity'){
											$entity           = ossn_get_entity($annotation->subject_guid);
											$option['entity'] = $entity;
										}
										if($annotation->type == 'comments:post'){
											$post           = ossn_get_object($annotation->subject_guid);
											if($post){
												$option['post']['post_guid'] = $post->guid;	
												$option['post']['post_type'] = $post->type;	
												$option['post']['owner_guid'] = $post->owner_guid;	
												if($post->type == 'group'){
													$group = ossn_get_group_by_guid($post->owner_guid);
													if($group){
														$option['group']['guid'] = $group->guid;
														$option['group']['title'] = $group->title;
														$option['group']['ismember'] = $group->isMember(NULL, $owner_guid);
													}
												}
											}
										}
								}
						}
						if($litem->type == "group:joinrequest"){
								$group = ossn_get_group_by_guid($litem->subject_guid);
								if($group){
									$option['group']['guid'] = $group->guid;
									$option['group']['title'] = $group->title;
									$option['group']['ismember'] = $group->isMember(NULL, $owner_guid);
								}								
						}
						$listp[] = $option;
				}
		}
		$options['count'] = true;
		$count            = $notifications->searchNotifications($options);
		
		$params['OssnServices']->successResponse(array(
				'list' => $listp,
				'count' => $count,
				'offset' => input('offset', '', 1)
		));
		
} else {
		$params['OssnServices']->throwError('103', ossn_print('ossnservices:nouser'));
}