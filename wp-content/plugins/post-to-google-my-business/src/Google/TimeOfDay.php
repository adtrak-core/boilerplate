<?php


namespace PGMB\Google;

use InvalidArgumentException;

class TimeOfDay extends AbstractGoogleJsonObject {
	private $hours, $minutes, $seconds, $nanos;

	public function __construct($hours = 0, $minutes = 0, $seconds = 0, $nanos = 0) {
		$this->setHours($hours);
		$this->setMinutes($minutes);
		$this->setSeconds($seconds);
		$this->setNanos($nanos);
	}

	public function setHours($hours){
		if(!is_numeric($hours) || $hours < 0 || $hours > 23){
			throw new InvalidArgumentException(__('Hour should be from 0 to 23', 'post-to-google-my-business'));
		}
		$this->jsonOutput['hours'] = $this->hours = $hours;
	}

	public function setMinutes($minutes){
		if(!is_numeric($minutes) || $minutes < 0 || $minutes > 59){
			throw new InvalidArgumentException(__('Minutes should be between 0 and 59', 'post-to-google-my-business'));
		}
		$this->jsonOutput['minutes'] = $this->minutes = $minutes;
	}

	public function setSeconds($seconds){
		if(!is_numeric($seconds) || $seconds < 0 || $seconds > 59){
			throw new InvalidArgumentException(__('Seconds should be between 0 and 59', 'post-to-google-my-business'));
		}
		$this->jsonOutput['seconds'] = $this->seconds = $seconds;
	}

	public function setNanos($nanos){
		if(!is_numeric($nanos) || $nanos < 0 || $nanos > 999999999){
			throw new InvalidArgumentException(__('Nanos should be between 0 and 999,999,999', 'post-to-google-my-business'));
		}
		$this->jsonOutput['nanos'] = $this->nanos = $nanos;
	}

	public function __toString() {
		return $this->hours . ":" . $this->minutes . ":" . $this->seconds . "." . $this->nanos;
	}

	public static function fromArray($array){
		return new self($array['hours'], $array['minutes'], $array['seconds'], $array['nanos']);
	}
}
