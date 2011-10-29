<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Taskdate {
	
	public function current_date()
	{
		return array(
			'year' => date('Y'),
			'month' => date('m'),
			'today_slug' => date("Y-m-d"),
			'uri_date' => 0
		);
	}

	public function past_date($uri_date)
	{
		$date_arr = explode('-', $uri_date);
		$year = $date_arr[0];
		$month = $date_arr[1];
		$day = $date_arr[2];
 
     	return array(
     		'uri_date' => $uri_date,
    		'year' => $year,
    		'month' => $month,
    		'day' => $day,
    		'yesterday' => date("Y-m-d", mktime( 0, 0, 0, $month, $day-1, $year )),
    		'today_short' => date("m/d/Y", mktime( 0, 0, 0, $month, $day, $year )),
    		'today_long' => date("D M j, Y", mktime( 0, 0, 0, $month, $day, $year )),
    		'tomorrow' => date("Y-m-d", mktime( 0, 0, 0, $month, $day+1, $year ))
    	);
	}
	
}