<div class="mobile-top-bar Fixed">

    <?php if ( (isset($_COOKIE['area']) && $_COOKIE['area'] !='uk') || (isset($_GET['a']) && $_GET['a'] !='uk') ) : ?>

		<?php /* HTML with PPC numbers */ ?>

		<p class="mobile-top-bar__btn mobile-top-bar__phone"><i class="fa fa-phone"></i> <span class="ld-phonenumber"><?php the_field('default_phone_number', 'option'); ?></span></p>

    <?php else : ?>

		<?php /* HTML with SEO numbers */ ?>

		<a class="mobile-top-bar__btn mobile-top-bar__phone js-toggle-location-numbers"><i class="fa fa-phone"></i> Call us <i class="fa fa-caret-down"></i></a>

    <?php endif; ?>

    <a class="mobile-top-bar__btn mobile-top-bar__menu menu-btn" href="#primary-navigation"><i class="fa fa-bars"></i> Menu</a>

	<div class="mobile-top-bar__location-numbers">
		<p>
			<span class="ld-calltag"><?php the_field('default_call_tag', 'option'); ?></span>
			<span class="ld-phonenumber"><?php the_field('default_phone_number', 'option'); ?></span>
		</p>
		<div class="ld-locationnumbers-list"></div>
	</div>

</div>