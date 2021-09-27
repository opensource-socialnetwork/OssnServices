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
$guid       = input('guid');
$email      = input('new_email');
$gender     = input('new_gender');
$first_name = input('new_first_name');
$last_name  = input('new_last_name');

$old_password = input('current_password');
$password     = input('new_password');

if($guid) {
		$user = ossn_user_by_guid($guid);
}
if($user) {
		$OssnUser           = new OssnUser;
		$OssnUser->password = $password;
		$OssnUser->email    = $email;
		
		if(empty($first_name) || empty($last_name) || empty($email)) {
				$params['OssnServices']->throwError('103', ossn_print('ossnservices:useredit:mindetails'));
		}
		if(!$OssnUser->isEmail()) {
				$params['OssnServices']->throwError('103', ossn_print('ossnservices:invalidemail'));
		}
		if($user->email !== $email) {
				if($OssnUser->isOssnEmail()) {
						$params['OssnServices']->throwError('103', ossn_print('ossnservices:emailalreadyinuse'));
				}
		}
		if(!empty($password) && !$OssnUser->isPassword()) {
				$length = ossn_call_hook('user', 'password:minimum:length', false, 6);
				$params['OssnServices']->throwError('103', ossn_print('ossnservices:invalidpassword', array(
						$length
				)));
		}
		//if not algo specified when user edit md5 is used #1503
		if(!empty($password) && isset($user->password_algorithm) && !empty($user->password_algorithm)) {
				$OssnUser->setPassAlgo($user->password_algorithm);
		}
		$OssnDatabase     = new OssnDatabase;
		$params['table']  = 'ossn_users';
		$params['wheres'] = array(
				"guid='{$user->guid}'"
		);
		
		$params['names']  = array(
				'first_name',
				'last_name',
				'email'
		);
		$params['values'] = array(
				$first_name,
				$last_name,
				$email
		);
		//v5.1 as OssnUser:VerifyPassowrd is private method 
		$VerifyPassowrd = function($password, $salt, $hash, $algo){
				switch($algo) {
						case 'bcrypt':
						case 'argon2i':
								return password_verify($password . $salt, $hash);
								break;
				}
				$password = md5($password . $salt);
				if($password === $hash) {
						return true;
				}
				return false;				
		};
		if(empty($old_password) || !empty($old_password) && !$VerifyPassowrd($old_password, $user->salt, $user->password, $user->password_algorithm)){
					$params['OssnServices']->throwError('103', ossn_print('ossnservices:invalidoldpassword'));	
		}		
		if(!empty($password)) {
				$salt     = $OssnUser->generateSalt();
				$password = $OssnUser->generate_password($password, $salt);
				
				$params['names'][3]  = 'password';
				$params['names'][4]  = 'salt';
				$params['values'][3] = $password;
				$params['values'][4]  = $salt;
	
		}
		if($OssnDatabase->update($params)) {
				$genders = array(
						'male',
						'female'
				);
				if(!empty($gender) && in_array($gender, $genders)) {
						$user->data->gender = $gender;
						$user->save();
				}
				$user = $params['OssnServices']->setUser(ossn_user_by_guid($user->guid)); //get user again with new contents
				$params['OssnServices']->successResponse($user);
		}
}
$params['OssnServices']->throwError('103', ossn_print('ossnservices:usereditfailed'));