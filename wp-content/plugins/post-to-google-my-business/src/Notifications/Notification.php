<?php


namespace PGMB\Notifications;


interface Notification {
	public function get_section();
	public function get_identifier();
	public function get_data();
}
