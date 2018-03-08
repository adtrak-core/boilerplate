<?php
//----------------------------------------------------------------------------------------------------------------------
// Blog Sidebar
//----------------------------------------------------------------------------------------------------------------------
if (is_home() || is_singular('post') || is_month() || is_category() || is_search()) : ?>

	<div class="blog-sidebar">

		<div class="list">
			<h3>Archives</h3>
			<ul>
				<?php wp_get_archives( 'type=monthly' ); ?>
			</ul>
		</div>

		<div class="list">
			<h3>Categories</h3>
			<ul>
			    <?php wp_list_categories( array(
			        'title_li' => ''
			    ) ); ?>
			</ul>
		</div>

	</div>

<?php endif; ?>