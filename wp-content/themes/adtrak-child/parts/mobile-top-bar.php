<div class="mobile-top-bar Fixed">

    <?php do_action("ld_mobile_top", "Call us"); ?>

	<a class="mobile-top-bar__btn mobile-top-bar__menu menu-btn" href="#navigation"><i class="fa fa-bars"></i> Menu</a>

	<div class="mobile-top-bar__location-numbers">
			<?php do_action("ld_default"); ?>
			<?php do_action("ld_list", false, "inline"); ?>
	</div>

</div>