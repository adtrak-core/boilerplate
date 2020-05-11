<?php


namespace PGMB\Google;

use InvalidArgumentException;

class TimeInterval extends AbstractGoogleJsonObject {
	private $startDate, $startTime, $endDate, $endTime;

	public function __construct(Date $startDate, TimeOfDay $startTime, Date $endDate, TimeOfDay $endTime) {
		$this->setStartDate($startDate);
		$this->setStartTime($startTime);
		$this->setEndDate($endDate);
		$this->setEndTime($endTime);
		$this->isStartBeforeEnd();
	}

	public function setStartDate(Date $startDate){
		$this->jsonOutput['startDate'] = $this->startDate = $startDate;
	}

	public function setStartTime(TimeOfDay $startTime){
		$this->jsonOutput['startTime'] = $this->startTime = $startTime;
	}

	public function setEndDate(Date $endDate){
		$this->jsonOutput['endDate'] = $this->endDate = $endDate;
	}

	public function setEndTime(TimeOfDay $endTime){
		$this->jsonOutput['endTime'] = $this->endTime = $endTime;
	}

	public function setAllDay($eventAllDay){
		if($eventAllDay){
			$this->jsonOutput['startTime'] = new \stdClass();
			$endTime = new TimeOfDay(23,59,59);
			$this->jsonOutput['endTime'] = $endTime;
		}
	}

	public function isStartBeforeEnd(){
		$start = strtotime($this->startDate . " " . $this->startTime);
		$end = strtotime($this->endDate . " " .$this->endTime);
		if($end <= $start){
			throw new InvalidArgumentException(__("End date is before start date", 'post-to-google-my-business'));
		}
	}

	public static function fromArray( $array ) {
		return new self(
			Date::fromArray($array['startDate']),
			TimeOfDay::fromArray($array['startTime']),
			Date::fromArray($array['endDate']),
			TimeOfDay::fromArray($array['endTime'])
		);
	}
}
