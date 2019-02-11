<?php
//----------------------------------------------------------------------------------------------------------------------
// Blog Sidebar
//----------------------------------------------------------------------------------------------------------------------
if (is_home() || is_singular('post') || is_month() || is_category() || is_search()) : ?>

	<div class="news-block">
		<?php get_search_form(); ?>
	</div>

	<div class="news-block">
		<h3 class="news-block__title">Archives</h3>
		<ul>
			<?php wp_get_archives( 'type=monthly' ); ?>
		</ul>
	</div>

	<div class="news-block">
		<h3 class="news-block__title">Categories</h3>
		<ul>
		    <?php wp_list_categories( array(
		        'title_li' => ''
		    ) ); ?>
		</ul>
	</div>

<?php else: ?>


<?php endif; ?>