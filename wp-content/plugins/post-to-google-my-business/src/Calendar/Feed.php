<?php


namespace PGMB\Calendar;


use Exception;
use PGMB\ParseFormFields;
use PGMB\PostTypes\SubPost;
use PGMB\Vendor\Rarst\WordPress\DateTime\WpDateTime;
use PGMB\Vendor\Rarst\WordPress\DateTime\WpDateTimeZone;
use WP_Query;

class Feed {


	public function init($hook){
		add_action("wp_ajax_{$hook}", [$this, 'generate']);
	}

	public function generate(){
		try {
			$start_date = new WpDateTime( $_REQUEST['start'], WpDateTimeZone::getWpTimezone());
			$end_date = new WpDateTime($_REQUEST['end'], WpDateTimeZone::getWpTimezone());
		} catch ( Exception $e ) {
			//Todo: check how fullcalendar can handle errors
		}

		$post_ids = $this->get_gmb_posts($start_date->getTimestamp(), $end_date->getTimestamp());
		$events = $this->prepare_events($post_ids);
		wp_send_json($events);
	}

	/**
	 * @param $post_ids
	 *
	 * @return array
	 * @throws Exception
	 */
	protected function prepare_events($post_ids) {
		$now = new WpDateTime('now', WpDateTimeZone::getWpTimezone());
		$events = [];
		foreach($post_ids as $post_id){
			$parent_post_id = wp_get_post_parent_id($post_id);
			$post_date_timestamp = get_post_meta($post_id, '_mbp_post_publish_date', true);

			$form_fields = get_post_meta($post_id, 'mbp_form_fields', true);

			$posts_have_error = !empty(get_post_meta($post_id, 'mbp_errors', true));

			$post_date = new WpDateTime();
			$post_date->setTimestamp($post_date_timestamp);
			$post_date->setTimezone(WpDateTimeZone::getWpTimezone());
			$live = $post_date <= $now;
			$events[] = [
				"title"     => get_the_title($parent_post_id),
				"start"     => $post_date->format(WpDateTime::ISO8601),
				"end"       => null,
				"url"       => get_edit_post_link($parent_post_id, false),
				"color"     => $live ? ($posts_have_error ? "#DE2E30" : "#4CAF50") : "#2196F3",
				"live"      => $live ? true : false,
				"hasError" => $posts_have_error,
				"repost"    => isset($form_fields['mbp_repost']) && $form_fields['mbp_repost'],
				"topictype"  => $form_fields['mbp_topic_type']
			];
		}

		return $events;

//		$objDateTime = new \DateTime('NOW');
//		return [
//			[
//				"title" => "Testevent",
//				"start" => $objDateTime->format(\DateTime::ISO8601),
//				"end"   => null,
//			]
//		];
	}

	protected function get_gmb_posts($start_date_timestamp, $end_date_timestamp){
		$args = [
			'post_type' => 	SubPost::POST_TYPE,
			'posts_per_page'    => -1,
			'fields' => 'ids',
			'meta_query' => [
				'relation' => 'AND',
				[
					'key'       => '_mbp_post_publish_date',
					'value'     => $start_date_timestamp,
					'compare'   => '>=',
					'type'      => 'DECIMAL'
				],
				[
					'key'       => '_mbp_post_publish_date',
					'value'     => $end_date_timestamp,
					'compare'   => '<=',
					'type'      => 'DECIMAL'
				],
			]
		];

		$query = new WP_Query( $args );
		return $query->posts;
	}
}
