<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */
 
/**
 * Smarty relative date / time plugin
 *
 * Type:     modifier<br>
 * Name:     relative_datetime<br>
 * Date:     March 18, 2009
 * Purpose:  converts a date to a relative time
 * Input:    date to format
 * Example:  {$datetime|relative_datetime}
 * @author   Eric Lamb <eric@ericlamb.net>
 * @version 1.0
 * @param string
 * @return string
 */
function smarty_modifier_relative_datetime($timestamp)
{
	if(!$timestamp){
		return 'N/A';
	}
 
	$timestamp = (int)strtotime($timestamp);
	$difference = time() - $timestamp;
	$periods = array("sekund", "min.", "time", "dag", "uke","måned", "år", "tiår");
	$lengths = array("60","60","24","7","4.35","12","10");
	$total_lengths = count($lengths);
 
	if ($difference > 0) { // this was in the past
		$ending = " siden";
	} else { // this was in the future
		$difference = -$difference;
		$ending = " til";
	}
	//return;
 
	for($j = 0; $difference > $lengths[$j] && $total_lengths > $j; $j++) {
		$difference /= $lengths[$j];
	}
 
	$difference = round($difference);
    if ($difference != 1 && $periods[$j] != 'år' && $periods[$j] != 'min.') {
        if ($periods[$j] != 'time' && $periods[$j] != 'uke') {
            $periods[$j].= "er";
        } else {
            $periods[$j].= "r";
        }
	}
 
	$text = "$difference $periods[$j] $ending";

	return $text;
}
?>
