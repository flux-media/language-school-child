<?php
/**
 * Avengerschool Product Class.
 * 
 * @author leehankyeol
 * @version 0.0.1
 */

class ASProduct {
	public $product;
	public $date;

	protected $PAST_STANDARD_IN_SEC = 60 * 60 * 2; // 2 hours.

	protected $FULL_REFUND_STANDARD_IN_SEC = 60 * 60 * 24 * 7; // 1 week.
	protected $PARTIAL_REFUND_STANDARD1_IN_SEC = 60 * 60 * 72; // 72 hours.
	protected $PARTIAL_REFUND_STANDARD2_IN_SEC = 60 * 60 * 24; // 24 hours.

	public function __construct($product) {
		$this->product = $product;
		$this->date = $this->getDateTime(get_post_meta( $this->product->id, 'as_date', true ));
	}

	public function is_bundle() {
		return $this->product->product_type == 'bundle';
	}

	public function is_past() {
		$is_past = false;
		
		if ($this->date === false) {
			// Let $is_past be false.
		} else {
			$diff = $this->get_diff_from_now();
			if ($diff < $this->PAST_STANDARD_IN_SEC) {
				$is_past = true;
			}
		}

		return $is_past;
	}

	public function get_refund_rate() {
		$rate = 1;

		if ($this->is_bundle()) {
			$bundle = new WC_Product_Bundle($this->product->id);
			$bundled_items = $bundle->get_bundled_items();

			// Collect all dates of bundled items as an array.
			$bundled_items_dates = array();
			foreach ($bundled_items as $bundled_item) {
				array_push($bundled_items_dates, $this->getDateTime(get_post_meta($bundled_item->product_id, 'as_date', true)));
			}

			// Sort the array, just in case.
			usort($bundled_items_dates, function($a, $b) {
				if ($a == $b) {
					return 0;
				}

				return $a < $b ? -1 : 1;
			});

			$now = new DateTime('now', new DateTimeZone('Asia/Seoul'));

			if ($bundled_items_dates[0]->getTimeStamp() - 60 * 60 * 24 * 7 > $now->getTimeStamp()) {
			} else if ($now->getTimeStamp() >= $bundled_items_dates[0]->getTimeStamp() - 60 * 60 * 24 * 7 && $now < $bundled_items_dates[0]) {
				$rate = 0.9;
			} else if ($now >= $bundled_items_dates[0] && $now < $bundled_items_dates[1]) {
				$rate = 0.65;
			} else if ($now >= $bundled_items_dates[1] && $now < $bundled_items_dates[2]) {
				$rate = 0.4;
			} else {
				$rate = 0;
			}
		} else {
			$diff = $this->get_diff_from_now();

			if ($diff >= $this->FULL_REFUND_STANDARD_IN_SEC) {
			} else if ($diff >= $this->PARTIAL_REFUND_STANDARD1_IN_SEC && $diff < $this->FULL_REFUND_STANDARD_IN_SEC) {
				$rate = 0.7;
			} else if ($diff >= $this->PARTIAL_REFUND_STANDARD2_IN_SEC && $diff < $this->PARTIAL_REFUND_STANDARD1_IN_SEC) {
				$rate = 0.5;
			} else {
				$rate = 0;
			}	
		}		

		return $rate;
	}

	private function getDateTime($date, $purify = true) {
		if ($purify) {
			// 2016.04.30 PM 7:30 -> 2016.04.30 7:30 PM
			$date = preg_replace("/([0-9.]+) ([ap]m) ([0-9:]+)/i", "$1 $3 $2", $date);	
		}
		return DateTime::createFromFormat('Y.m.d g:i A', $date, new DateTimeZone('Asia/Seoul'));
	}

	private function get_diff_from_now() {
		$now = new DateTime('now', new DateTimeZone('Asia/Seoul'));
		$interval_in_sec = $this->date->getTimeStamp() - $now->getTimeStamp();
		return $interval_in_sec;
	}
}
?>