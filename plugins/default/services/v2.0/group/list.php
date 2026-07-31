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
$offset      = input('offset');

$args = array(
		'type' => 'user',
		'subtype' => 'ossngroup',
);

$obj = new OssnObject();
$list = $obj->searchObject($args);

$args['count'] = true;
$count = $obj->searchObject($args);

$params['OssnServices']->successResponse(array(
		'list'     => $list,
		'count'    => $count,
		'offset'   => $offset,
));