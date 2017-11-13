	<div class="why-choose-us pad-1-1">

		<div class="container">

			<h2><?php the_field('why_header','options') ?></h2>

			<?php if (have_rows('why_choose_us','options')) :?>

			<ul>
				<?php while( have_rows('why_choose_us','options') ): the_row(); ?>
					<li><span><?php the_sub_field('icon','options'); ?></span> <?php the_sub_field('reason','options'); ?></li>
				<?php endwhile; ?>
			</ul>

			<?php endif; ?>

		</div>

	</div>