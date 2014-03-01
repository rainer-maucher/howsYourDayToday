<?php
/**
 * Simple DatabaseManager, establisches connection to database and executes queries or collects data
 *
 * Class Lib_DatabaseManager
 */
class Lib_DatabaseManager
{
	//Verbinden mit Server und Auswählen der DB
	public function __construct()
	{
		require(__dir__ . '/../config/database.php');

		mysql_connect($db_host, $db_username, $db_password) or die('error while connecting with database: '. mysql_error());
		mysql_select_db($db_name) or die('error while selecting database: '.mysql_error());
	}

	/**
	 * @param $query
	 * @return resource
	 */
	function query($query)
	{
		$result = mysql_query($query) or die('error in query: '. mysql_error().'<br />Query: '.$query);
		return $result;
	}

	/**
	 * reads data from db and returns anarray
	 *
	 * @param $query
	 * @return array
	 */
	public function read($query)
	{
		$data = mysql_query($query) or die('error in query: '. mysql_error().'<br />Query: '.$query);
		if (!$data) {
			$message  = 'Ungültige Abfrage: ' . mysql_error() . "\n";
			$message .= 'Gesamte Abfrage: ' . $query;
			die($message);
		}
		$result = array();
		while ($row = mysql_fetch_assoc($data)) {
			$result = $row;
		}

		return $result;
	}

	/**
	 * reads data from db and returns array with all rows
	 *
	 * @param $query
	 * @return array
	 */
	public function readRows($query)
	{
		$data = mysql_query($query) or die('error in query: '. mysql_error().'<br />Query: '.$query);
		if (!$data) {
			$message  = 'Ungültige Abfrage: ' . mysql_error() . "\n";
			$message .= 'Gesamte Abfrage: ' . $query;
			die($message);
		}

		$result = array();
		while ($row = mysql_fetch_assoc($data)) {
			$result[] = $row;
		}

		return $result;
	}

	/**
	 * Validate given data to prevent SQL-Injection
	 *
	 * @param array $data
	 * @return array
	 */
	public function validateData(array $data)
	{
		if (isset($data['user'])) {
			$data['user'] = mysql_real_escape_string($data['user']);
		}

		if (isset($data['mood'])) {
			$data['mood'] = (int) $data['mood'];
		}

		return $data;
	}
}

