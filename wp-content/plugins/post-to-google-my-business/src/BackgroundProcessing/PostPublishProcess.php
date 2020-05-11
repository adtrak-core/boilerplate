<?php


namespace PGMB\BackgroundProcessing;


use stdClass;
use WP_Background_Process;

class PostPublishProcess extends WP_Background_Process implements BackgroundProcess {

	protected $action = 'mbp_background_process';
	/**
	 * @var string
	 */
	protected $batch_key;


	public function __construct() {
		parent::__construct();
	}

	/**
	 * Task
	 *
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 *
	 * @param mixed $item Queue item to iterate over.
	 *
	 * @return mixed
	 */
	protected function task( $item ) {
		do_action_ref_array($item['action'], $item['args']);



		return false;
	}

	public function save() {
		$key = $this->generate_key();

		if ( ! empty( $this->data ) ) {
			update_site_option( $key, $this->data );
		}
		$this->batch_key = $key;
		return $this;
	}

	public function get_batch_key(){
		return $this->batch_key;
	}

	public function get_batch_by_key($key){
		return get_option($key);
	}

	protected function complete() {
		parent::complete();

	}
}
