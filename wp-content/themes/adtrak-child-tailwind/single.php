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

<div class="container p-4 md:p-8 lg:px-0 lg:flex flex-wrap flex-grow">

		<?php if (have_posts()): while (have_posts()): the_post(); ?>

			<main class="lg:w-2/3 lg:pr-16">

				<article>
					<h1><?php the_field('h1'); ?></h1>
					<?php the_content(); ?>
				</article>

			</main>

      <?php echo do_shortcode("[ninja_form id=1]"); ?>

			<aside class="bg-gray-100 lg:w-1/3">
				<?php get_sidebar(); ?>
			</aside>

		<?php endwhile; endif; ?>

</div>

<?php get_footer(); ?>

<?php if (is_single()) {   //  displaying a single blog post ?>
<script type="text/javascript">
  jQuery("a[href$='news/']").parent().addClass('current-menu-item');
</script>
<?php } ?>
