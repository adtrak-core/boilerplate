<?php


namespace PGMB\Notifications;


class NotificationManager {

	private $prefix;
	private $notifications;
	private $ignored_notifications;

	public function __construct($prefix) {
		$this->prefix = $prefix;
	}

	protected function load_notifications(){
		$this->ignored_notifications = get_option("{$this->prefix}_ignored_notifications");
		if(empty($this->ignored_notifications)){
			$this->ignored_notifications = [];
		}

		$this->notifications = get_option("{$this->prefix}_notifications");
		if(empty($this->notifications)){
			$this->notifications = [];
		}
	}

	protected function save_notifications(){
		update_option("{$this->prefix}_notifications", $this->notifications);
		update_option("{$this->prefix}_ignored_notifications", $this->ignored_notifications);
	}

	public function get_notifications($section, $limit = 5){
		if(!$this->notifications){ $this->load_notifications(); }
		if(!isset($this->notifications[$section]) || !is_array($this->notifications[$section])){ return []; }

		return array_slice(array_reverse($this->notifications[$section]),0, $limit);
	}

	public function add_notification(Notification $notification){
		if(!$this->notifications){ $this->load_notifications(); }
		if(isset($this->ignored_notifications[$notification->get_section()][$notification->get_identifier()])){
			return;
		}

		$this->notifications[$notification->get_section()][$notification->get_identifier()] = $notification->get_data();

		$this->save_notifications();
	}

	public function notification_count($section){
		return count($this->get_notifications($section));
	}

	public function delete_notification($section, $identifier, $ignore = false){
		if(!$this->notifications){ $this->load_notifications(); }
		unset($this->notifications[$section][$identifier]);
		if($ignore){
			$this->ignored_notifications[$section][$identifier] = true;
		}
		$this->save_notifications();
	}

}
