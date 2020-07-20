<?php 

$context = Timber::context();
$timber_post = new Timber\Post();
$context['post'] = $timber_post;

$context['archives'] = wp_get_archives( array('type=>monthly', 'echo'=>0 ));
$context['categories'] = wp_list_categories(array('title_li' => '', 'echo'=>0));

Timber::render( [ 'single.twig' ], $context );

if (is_single()) { ?>
<script type="text/javascript">
  document.querySelector('.current_page_parent').classList.add('current-menu-item');
</script>
<?php }
