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
$username = input('username');
$password = input('password');

//check if username is email
if(strpos($username, '@') !== false) {
		$user = ossn_user_by_email($username);
} else {
		$user = ossn_user_by_username($username);
}
if($user) {
		if($user->isUserVALIDATED()) {
				$login           = new OssnUser;
				$login->username = $username;
				$login->password = $password;
				if($login->Login()) {
						$user = $params['OssnServices']->setUser($user);
						$params['OssnServices']->successResponse($user);
				}
				$params['OssnServices']->throwError('105', ossn_print('login:error'));
		} else {
				$params['OssnServices']->throwError('104', ossn_print('ossnservices:usernotvalidated'));
		}
}
$params['OssnServices']->throwError('103', ossn_print('ossnservices:nouser'));