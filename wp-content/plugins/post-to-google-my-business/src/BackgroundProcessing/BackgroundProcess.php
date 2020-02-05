<?php


namespace PGMB\BackgroundProcessing;


interface BackgroundProcess {
	/**
	 * Add item to the processing queue
	 *
	 * @param $item
	 *
	 * @return BackgroundProcess this
	 */
	public function push_to_queue($item);

	/**
	 * Save the processing queue
	 *
	 * @return BackgroundProcess this
	 */
	public function save();

	/**
	 * Start the batch processing process
	 *
	 * @return void
	 */
	public function dispatch();
}
