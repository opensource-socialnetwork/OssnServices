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
$en = array(
			'ossnservices' => 'Web Services',
			'ossnservices:apikey' => 'API KEY (please keep it secret)',
			'ossnservices:regenerate:key' => 'Regenerate',
			'ossnservices:generated' => 'Generated',
			'ossnservices:invalidmethod' => 'Invalid Method',
			'ossnservices:success' => 'Request successfully executed',
			'ossnservices:noresponse' => 'The requested method have no paypload for you',
			'ossnservices:nouser' => 'No such a user exists',
			'ossnservices:usernotvalidated' => 'User not validated',
			'ossnservices:invalidkeytoken' => 'Invalid API KEY token',
			'ossnservices:invalidversion' => 'Invalid API version',
			'ossnservices:invalidemail' => 'Invalid email address',
			'ossnservices:emailalreadyinuse' => 'Email address already in use, please use different email address',
			'ossnservices:invalidpassword' => 'Invalid password, please make sure password is minimum of length %s',
			'ossnservices:usereditfailed' => 'User modification has been failed!',
			'ossnservices:useredit:mindetails' => 'Please provide the new_email, new_first_name , new_last_name',
			'ossnservices:useradd:allfields' => 'Please make sure you provided all the fields, missing (%s)',
			'ossnservices:useradd:invalidgender' => 'Invalid gender, must be male or female',
			'ossnservices:empty:field:one:more' => 'One or more input expected , is empty. Please make sure you send all required inputs',
			'ossnservices:comment:failed:add' => 'Can not add the comment',
			'ossnservices:componnt:notfound' => 'One or more component required for this request can not be found on remote server',
			'ossnservices:wall:failed:add' => 'Can not add wall post',
			'ossnservices:invalidoldpassword' => 'Invalid current password',
			'ossnservices:invalidgroup' => 'Invalid group',
			'ossnservices:groupnomembers' => 'No Members',
			'ossnservices:groupnorequests' => 'No Requests',
			'ossnservices:invalidowner' => 'Invalid Owner',
			'ossnservices:notification:cannotmark' => 'Can not mark notification as read',
			'ossnservices:messagecannotblank' => 'Message can not be blank',
			'ossnservices:messagesendfailed' => 'Message can not be sent',
			'ossnservices:messagedeletefailed' => 'Message delete failed',
			'ossnservices:cannotdelete:comment' => 'Can not able to delete the comment!',
			'ossnservices:cannotaddalbum:photo' => 'Can not add photo into album',
			'ossnservices:cannotaddalbum' => 'Can not able to create album',
			'ossnservices:cannotdelete:photo' => 'Can not delete photos',
			'ossnservices:group:create:error' => 'Can not create group!',
			'ossnservices:oneoremore:invalid:input' => 'One or more input supplied is invalid',
			'ossnservices:like:failed:add' => 'Reaction add failed',
			'ossnservices:unlike:failed:add' => 'Unlike failed to set',
);
ossn_register_languages('en', $en);