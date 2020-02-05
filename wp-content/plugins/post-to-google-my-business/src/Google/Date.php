<?php


namespace PGMB\Google;

use InvalidArgumentException;

/**
 * Class Date
 * @package PGMB\Google
 *
 * @property int year
 * @property int month
 * @property int day
 */
class Date extends AbstractGoogleJsonObject {
	/**
	 * @var int $year
	 * @var int $day
	 * @var int $month
	 */
	private $year, $day, $month;

	/**
	 * Date constructor.
	 *
	 * @param int $year
	 * @param int $month
	 * @param int $day
	 */
	public function __construct($year = 0, $month = 0, $day = 0) {
		$this->setYear($year);
		$this->setMonth($month);
		$this->setDay($day);
	}

	/**
	 * @param $year
	 */
	public function setYear($year){
		if(!is_numeric($year) || $year < 0 || $year > 9999){
			throw new InvalidArgumentException(__('Year must be from 1 to 9999, or 0 if specifying a date without a year.', 'post-to-google-my-business'));
		}
		$this->jsonOutput['year'] = $this->year = $year;
	}

	/**
	 * @param $month
	 */
	public function setMonth($month){
		if(!is_numeric($month) || $month < 0 || $month > 12){
			throw new InvalidArgumentException(__('Month must be from 1 to 12, or 0 if specifying a year without a month and day. ', 'post-to-google-my-business'));
		}
		$this->jsonOutput['month'] = $this->month = $month;
	}

	/**
	 * @param $day
	 */
	public function setDay($day){
		if(!is_numeric($day) || $day < 0 || $day > 31){
			throw new InvalidArgumentException(__('Day must be from 1 to 31 and valid for the year and month, or 0 if specifying a year by itself or a year and month where the day is not significant. ', 'post-to-google-my-business'));
		}
		$this->jsonOutput['day'] = $this->day = $day;
	}

	/**
	 * Convert the date to a string for further handling
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->year . "-" . $this->month . "-" . $this->day;
	}

	/**
	 * Build Date object from array
	 *
	 * @param $array
	 *
	 * @return Date
	 */
	public static function fromArray( $array ) {
		return new self($array['year'], $array['month'], $array['day']);
	}
}
