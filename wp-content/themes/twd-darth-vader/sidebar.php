<?php if ( is_page() ) : ?>

	<?php include locate_template('parts/ctas.php'); ?>

<?php elseif (is_home() || is_singular('post') || is_month() || is_category() || is_search()) : ?>

	<h3>All Categories</h3>
	<?php
		$categories =  get_categories();
		echo '<ul class="cats">';
		foreach  ($categories as $category) {
		  echo '<li><a href="'.get_category_link($category->cat_ID).'">'. $category->cat_name .' <small>('.$category->count.')</small></a></li>';
		}
		echo '</ul>';
	?>

<?php endif; ?>