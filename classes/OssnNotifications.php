<?php
/**
 * Open Source Social Network
 *
 * @package   (softlab24.com).ossn
 * @author    OSSN Core Team <info@softlab24.com>
 * @copyright (C) SOFTLAB24 LIMITED
 * @license   Open Source Social Network License (OSSN LICENSE)  http://www.opensource-socialnetwork.org/licence
 * @link      https://www.opensource-socialnetwork.org/
 */
namespace Ossn\Component; 
class Notifications extends \OssnDatabase {
		/**
		 * Search Notifcations
		 *
		 * @param array $params A valid options in format,
		 * @param string $params['type'] Notification type 		
		 * @param string $params['owner_guid'] Notification owner guid	
		 * @param string $params['poster_guid'] Notification poster guid	
		 * @param string $params['subject_guid'] Notifcation subject guid 		
		 * @param string $params['item_created'] Notifcation time_created 		
		 * @param string $params['item_guid'] Notifcation item guid 		
		 * @param string $params['count'] If you wanted to count then true 		
		 * @param string $params['viewed'] If viewed true, if not then false 		
		 * @param string $params['guid'] Notifcation guid 		
		 * @param string $params['order_by'] Order list , default ASC guid 		
		 * 
		 * reutrn array|false;
		 *
		 */		
		public function searchNotifications(array $params = array()){
				$default = array(
						'guid' => false,
						'type' => false,
						'poster_guid' => false,
						'owner_guid' => false,
						'subject_guid' => false,
						'time_created' => false,
						'item_guid' => false,
						'limit' => false,
						'order_by' => false,
						'offset' => input('offset', '', 1),
						'page_limit' => ossn_call_hook('pagination', 'per_page', false, 10), //call hook for page limit
						'count' => false
				);
				$options = array_merge($default, $params);
				$wheres  = array();
				//prepare limit
				$limit   = $options['limit'];
				
				//validate offset values
				if(!empty($options['limit']) && !empty($options['limit']) && !empty($options['page_limit'])) {
						$offset_vals = ceil($options['limit'] / $options['page_limit']);
						$offset_vals = abs($offset_vals);
						$offset_vals = range(1, $offset_vals);
						if(!in_array($options['offset'], $offset_vals)) {
								return false;
						}
				}
				//get only required result, don't bust your server memory
				$getlimit = $this->generateLimit($options['limit'], $options['page_limit'], $options['offset']);
				if($getlimit) {
						$options['limit'] = $getlimit;
				}
				//search notifications
				if(!empty($options['guid'])) {
						$wheres[] = "n.guid='{$options['guid']}'";
				}
				if(!empty($options['type'])) {
						$wheres[] = "n.type='{$options['type']}'";
				}
				if(!empty($options['owner_guid'])) {
						$wheres[] = "n.owner_guid ='{$options['owner_guid']}'";
				}
				if(!empty($options['poster_guid'])) {
						$wheres[] = "n.poster_guid ='{$options['poster_guid']}'";
				}				
				if(!empty($options['subject_guid'])) {
						$wheres[] = "n.subject_guid ='{$options['subject_guid']}'";
				}		
				if(!empty($options['item_guid'])) {
						$wheres[] = "n.item_guid ='{$options['item_guid']}'";
				}			
				if(!empty($options['time_created'])) {
						$wheres[] = "n.time_created ='{$options['time_created']}'";
				}							
				if(isset($options['viewed']) && $options['viewed'] == true) {
						$wheres[] = "n.viewed =''";
				}							
				if(isset($options['viewed']) && $options['viewed'] == false) {
						$wheres[] = "n.viewed IS NULL";
				}		
				if(isset($options['wheres']) && !empty($options['wheres'])) {
						if(!is_array($options['wheres'])) {
								$wheres[] = $options['wheres'];
						} else {
								foreach($options['wheres'] as $witem) {
										$wheres[] = $witem;
								}
						}
				}				
				if(empty($wheres)){
						return false;	
				}
				$params             = array();
				$params['from']     = 'ossn_notifications as n';
				$params['params']   = array(
						'n.*',
				);
				$params['wheres']   = array(
						$this->constructWheres($wheres)
				);
				$params['order_by'] = $options['order_by'];
				$params['limit']    = $options['limit'];
				
				if(!$options['order_by']) {
						$params['order_by'] = "n.guid ASC";
				}
				if(isset($options['group_by']) && !empty($options['group_by'])) {
						$params['group_by'] = $options['group_by'];
				}					
				//override params
				if(isset($options['params']) && !empty($options['params'])){
						$params['params'] = $options['params'];
				}			
				//prepare count data;
				if($options['count'] === true) {
						unset($params['params']);
						unset($params['limit']);
						$count           = array();
						$count['params'] = array(
								"count(*) as total"
						);
						$count           = array_merge($params, $count);
						return $this->select($count)->total;
				}
				$fetched_data = $this->select($params, true);
				if($fetched_data){
					foreach($fetched_data as $item){
							$results[] = arrayObject($item, get_class($this));	
					}
					return $results;
				}
				return false;
		}
}
