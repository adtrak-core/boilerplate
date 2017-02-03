<div class="phone-top-right pad-1-1">

    <?php if ( (isset($_COOKIE['area']) && $_COOKIE['area'] !='uk') || (isset($_GET['a']) && $_GET['a'] !='uk') ) : ?>

         <?php /* HTML with PPC numbers */ ?>

        <p class="phone"><i class="fa fa-phone"></i> <span class="ld-calltag"><?php the_field('prefix_phone_number', 'option'); ?></span> <span class="ld-phonenumber"><?php the_field('default_phone_number', 'option'); ?></span></p>

    <?php else : ?>

         <?php /* HTML with SEO numbers */ ?>

        <p class="phone"><i class="fa fa-phone"></i> <span class="ld-calltag"><?php the_field('prefix_phone_number', 'option'); ?></span> <span class="ld-phonenumber"><?php the_field('default_phone_number', 'option'); ?></span></p>

        <div class="ld-locationnumbers"></div>

    <?php endif; ?>

</div>