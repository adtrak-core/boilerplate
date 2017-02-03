<script>
var ld_var = ld_var || {};
ld_var['ld_json'] = '<?php echo get_theme_file_uri() ?>/js/ld/phonenumbers.json';
ld_var['ld_message'] = 'Call Locally on Mobile';
</script>
<script src="http://adtrakld.co.uk/ld.js"></script>
<script>if(!window.ld_ready){document.write('<script src="<?php echo get_theme_file_uri() ?>/js/ld/ld.js"><\/script>');document.write('<script src="http://adtrakld.co.uk/alert.php?url='+encodeURIComponent(location.href)+'&version=Unknown&message=Primary%20script%20fail"><\/script>');}</script>

<!-- For location pages // NEEDS CUSTOM FIELD TO WORK -->
<?php if (is_singular('locations') && get_field('location_name')): ?>
	<script>
		var ld_var = ld_var || {}; 
		ld_var['ld_fixed'] = '<?php echo get_field('location_name'); ?>';
	</script>      
<?php endif; ?>