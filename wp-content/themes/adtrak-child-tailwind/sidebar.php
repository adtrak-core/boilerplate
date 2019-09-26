<?php
//----------------------------------------------------------------------------------------------------------------------
// Blog Sidebar
//----------------------------------------------------------------------------------------------------------------------
if (is_home() || is_singular('post') || is_month() || is_category() || is_search()) : ?>

	<?php 
		$block_title = false;
		$block_content = get_search_form($echo);
		include locate_template('parts/news-block.php');
	?>

	<?php 
		$block_title = "Archives";
		$block_content = wp_get_archives( array('type=>monthly', 'echo'=>0 ));
		include locate_template('parts/news-block.php');
	?>

	<?php 
		$block_title = "Categories";
		$block_content = wp_list_categories(array('title_li' => '', 'echo'=>0));
		include locate_template('parts/news-block.php');
	?>

<?php else: ?>


<?php endif; ?>