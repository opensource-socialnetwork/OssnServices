<?php
/**
 * Open Source Social Network
 *
 * @package   Open Source Social Network
 * @author    Open Social Website Core Team <info@softlab24.com>
 * @copyright SOFTLAB24 LIMITED
 * @license   Open Source Social Network License (OSSN LICENSE)  http://www.opensource-socialnetwork.org/licence
 * @link      https://www.opensource-socialnetwork.org/
 */
?>
<div>
	<strong><?php echo ossn_print('ossnservices:apikey');?></strong>
	<input class="margin-top-10" type="text" value="<?php echo ossn_services_apikey();?>" disabled="disabled" />
</div>
<div class="margin-top-10">
  <a class="btn btn-success" href="<?php echo ossn_site_url("action/services/admin/settings?confirm=1", true);?>"><?php echo ossn_print('ossnservices:regenerate:key');?></a>
</div>
