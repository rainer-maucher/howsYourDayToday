<?php
/**
 * Small functions that have no better place
 *
 * Class Lib_Helper
 */
class Lib_Helper {
	/**
	 * @return int
	 */
	public function getTodaysDate()
	{
		return date("Y.m.d",time());
	}
}