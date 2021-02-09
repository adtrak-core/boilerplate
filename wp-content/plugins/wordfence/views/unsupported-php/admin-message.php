<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents the unsupported PHP version  modal.
 */
?>
<div style="padding: 10px; border: 2px solid #00709e; background-color: #fff; margin: 20px 20px 10px 0px; color: #00709e">
	<img style="display: block; float: left; margin: 0 10px 0 0" src="<?php echo plugins_url('', WORDFENCE_FCPATH) . '/' ?>images/wordfence-logo.svg" alt="" width="35" height="35">
	<p style="margin: 10px">You are running PHP version <?php echo PHP_VERSION ?> that is not supported by Wordfence <?php echo WORDFENCE_VERSION ?>. Wordfence features will not be available until PHP has been upgraded. We recommend using PHP version 7.4, but Wordfence will run on PHP version 5.3 at a minimum.</p>
</div>