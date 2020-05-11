<?php
$taxonomy = new Timber\Term();

$parent_regions = get_terms([
  'taxonomy' => 'regions',
  'parent' => 0,
  'hide_empty' => false,
]);

$regions = get_terms([
  'taxonomy' => 'regions',
  'parent' => $taxonomy->term_id,
  'hide_empty' => false,
]);

$members = [
  'post_type' => 'members',
  'posts_per_page' => -1,
  'order' => 'ASC',
  'orderby' => 'title',
  'tax_query' => [
    [
      'taxonomy' => 'regions',
      'field' => 'term_id',
      'terms' => $taxonomy->term_id,
      'include_children' => 0,
    ]
  ]
];

$context = Timber::context();
$context['post'] = new Timber\Post();
$context['taxonomy'] = $taxonomy;
if($members) {
  $context['members'] = new Timber\PostQuery($members);
}
$context['parent_regions'] = $parent_regions;
if($regions) {
  $context['regions'] = $regions;
}
Timber::render( [ 'taxonomy-regions.twig' ], $context );
