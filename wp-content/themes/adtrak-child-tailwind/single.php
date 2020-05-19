<?php
/**
 * The template for rendering any single posts.
 * This is the template of single blog posts and custom post types. 
 * @author  Adtrak
 * @package AdtrakChild
 * @version 1.1.0
 */
?>

<?php
    get_header();
    include locate_template('parts/hero.php');
?>

<div class="container flex-wrap flex-grow lg:flex">

	<main class="lg:w-2/3 lg:pr-16">

		<article>

			<h1><?php the_title(); ?></h1>

			<?php the_content(); ?>

		<article>

	</main>

	<aside class="bg-gray-100 lg:w-1/3">
		<?php get_sidebar(); ?>
	</aside>		

</div>

<?php get_footer(); ?>

<?php if (is_single()) {   //  displaying a single blog post ?>
<script type="text/javascript">
  jQuery("a[href$='news/']").parent().addClass('current-menu-item');
</script>
<?php } ?>
