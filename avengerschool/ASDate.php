<?php
/**
 * Avengerschool Date
 * 
 * @author leehankyeol
 * @version 0.0.1
 */

class ASDate {

	protected $PAST_STANDARD_IN_SEC = 7200;
	protected $date;

	public function __construct($date) {
		// 2016.04.30 PM 7:30 -> 2016.04.30 7:30 PM
		$purified_date = preg_replace("/([0-9.]+) ([ap]m) ([0-9:]+)/i", "$1 $3 $2", $date);
		$this->date = DateTime::createFromFormat('Y.m.d g:i A', $purified_date, new DateTimeZone('Asia/Seoul'));
	}

	public function is_past() {
		$is_past = false;
		
		if ($this->date === false) {
			// Let $is_past be false.
		} else {
			$now = new DateTime('now', new DateTimeZone('Asia/Seoul'));
			$interval_in_sec = $this->date->getTimeStamp() - $now->getTimeStamp();
			if ($interval_in_sec < $this->PAST_STANDARD_IN_SEC) {
				$is_past = true;
			}
		}

		return $is_past;
	}
}
?>