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
$user['username']  = input('username');
$user['firstname'] = input('firstname');
$user['lastname']  = input('lastname');
$user['email']     = input('email');
$user['reemail']   = input('reemail');
$user['password']  = input('password');
$user['gender']    = input('gender');
$user['birthdate'] = input('birthdate');

/**$fields = ossn_user_fields_names();
foreach($fields['required'] as $field) {
		$user[$field] = input($field);
}**/

if(!empty($user)) {
		foreach($user as $field => $value) {
				if(empty($value)) {
						$json['error']    = '1';
						$missing_fields[] = $field;
				}
		}
}
if(isset($json['error']) && !empty($json['error'])) {
		$missing = array(
				implode(',', $missing_fields)
		);
		$params['OssnServices']->throwError('103', ossn_print('ossnservices:useradd:allfields', $missing));
}
$genders = array(
		'male',
		'female'
);
$gender  = $user['gender'];
if(!empty($gender) && !in_array($gender, $genders)) {
		$params['OssnServices']->throwError('103', 'invalidgender');
}
if($user['reemail'] !== $user['email']) {
		$params['OssnServices']->throwError('103', 'email:error:matching');
}


$add                  = new OssnUser;
$add->username        = $user['username'];
$add->first_name      = $user['firstname'];
$add->last_name       = $user['lastname'];
$add->email           = $user['email'];
$add->password        = $user['password'];
$add->sendactiviation = true;

foreach($fields as $items) {
		foreach($items as $field) {
				$add->{$field} = $user[$field];
		}
}
if(!$add->isUsername()) {
		$params['OssnServices']->throwError('103', 'username:error');
}
if(!$add->isPassword()) {
		$length = ossn_call_hook('user', 'password:minimum:length', false, 6);
		$params['OssnServices']->throwError('107', $length);
}
if($add->isOssnUsername()) {
		$params['OssnServices']->throwError('103', 'username:inuse');
}
if($add->isOssnEmail()) {
		$params['OssnServices']->throwError('103', 'email:inuse');
}
//check if email is valid email 
if(!$add->isEmail()) {
		$params['OssnServices']->throwError('103', 'email:invalid');
}
if($guid = $add->addUser()) {
		$user = ossn_user_by_guid($guid);
		$user = $params['OssnServices']->setUser($user, true); //didn't need all details
		$params['OssnServices']->successResponse($user);
} else {
		$params['OssnServices']->throwError('103', 'account:create:error:');
}