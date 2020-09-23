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
if(!com_is_active('OssnGroups')) {
		$params['OssnServices']->throwError('201', ossn_print('ossnservices:component:notfound'));
}
$attr               = array();
$attr['owner_guid'] = input('guid');
$attr['name']       = input('name');
$attr['privacy']    = input('privacy');
foreach($attr as $key => $item) {
		if(empty($item)) {
				$params['OssnServices']->throwError('106', ossn_print('ossnservices:empty:field:one:more'));
		}
}
if($attr['privacy'] != 2 && $attr['privacy'] != 1){
	$params['OssnServices']->throwError('103', ossn_print('ossnservices:oneoremore:invalid:input'));
}
$attr['description'] = input('description');
$add                 = new OssnGroup;
if($add->createGroup($attr)) {
		$guid = $add->getGuid();
		$params['OssnServices']->successResponse(ossn_get_group_by_guid($guid));
} else {
		$params['OssnServices']->throwError('103', ossn_print('ossnservices:group:create:error'));
}