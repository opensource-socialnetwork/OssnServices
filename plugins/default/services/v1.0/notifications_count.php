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
$guid = input('guid');
$types      = input('types');
if($guid) {
		$user = ossn_user_by_guid($guid);
}
if($user && com_is_active('OssnNotifications') && com_is_active('OssnMessages')) {
		
		$notifications = new \Ossn\Component\Notifications;
		$options       = array(
				'owner_guid' => $user->guid,
				'order_by' => 'n.guid DESC',
				'count' => true,
				'viewed' => false,
		);
		if(!empty($types)) {
				$json = json_decode(html_entity_decode($types), true);
				foreach($json as $item) {
						$typel[] = "'{$item}'";
				}
				$types_only        = implode(',', $typel);
				$options['wheres'] = "n.type IN ({$types_only})";
		}
		$countn  = $notifications->searchNotifications($options);
		
		$count_messages = 0;
		if(class_exists('OssnMessages')){
			$messages = new OssnMessages;
			$count_messages = $messages->countUNREAD($user->guid);
		}
		$friends = $user->getFriendRequests();
		
		$friends_c = 0;
		if($friends){
  			  $friends_c = count($friends);
		}	
		
		$params['OssnServices']->successResponse(array(
				'notifications' => $countn,
				'messages' => $count_messages,
				'friends' => $friends_c,
		));
		
} else {
		$params['OssnServices']->throwError('200', ossn_print('ossnservices:nouser'));
}