<?php
/**
 * Class that dispatches Actions by given Data
 *
 * Class Lib_Dispatcher
 */
class Lib_Dispatcher
{
	const CHART_MODE_HISTORY_OWN            = 'chartOptionOwn';
	const CHART_MODE_HISTORY_ALL_SUMMED     = 'chartOptionSummed';
	const CHART_MODE_HISTORY_ALL_SPLITTED   = 'chartOptionSplitted';

	/**
	 * @var DatabaseManager
	 */
	private $db;

	/**
	 * @var Helper
	 */
	private $helper;

	/**
	 * @param Lib_DatabaseManager $db
	 * @param Lib_Helper $helper
	 */
	public function __construct(Lib_DatabaseManager $db, Lib_Helper $helper) {
		$this->db = new Lib_DatabaseManager();
		$this->helper = new Lib_Helper();
	}

	/**
	 * minimalistic action dispatcher
	 *
	 * @param $action
	 * @param array $postData
	 *
	 * @throws Exception
	 */
	public function dispatchAction($action, array $postData)
	{
		$action = $action . 'Action';
		if (method_exists($this, $action)) {
			$this->$action($postData);
		}
		else {
			throw new Exception("Method $action not Found in Class " . __CLASS__);
		}
	}

	/**
	 * adds users mood
	 *
	 * @param array $data
	 */
	protected function addMoodAction(array $data)
	{
		$date = $this->helper->getCurrentDate();
		$data = $this->db->validateData($data);
		$query = "INSERT INTO data (user, date, mood) VALUES ('{$data['user']}', '$date', {$data['mood']}) ON DUPLICATE KEY UPDATE mood={$data['mood']}";

		$this->db->query($query);
	}

	/**
	 * @param array $data
	 */
	protected function getChartDataAction(array $data)
	{
		// Move to another place
		$mood = array();
		$mood['1'] = 'Please Shoot me';
		$mood['2'] = 'Bad';
		$mood['3'] = 'Good';
		$mood['4'] = 'All Righty right';
		$mood['5'] = 'Great';
		$mood['6'] = 'Fucking Awesome';

		$todaysDate = $this->helper->getTodaysDate();
		$data = $this->db->validateData($data);
		$query =  "SELECT mood, count(mood) as countMood FROM data WHERE date = '{$todaysDate}'" . PHP_EOL;
		$query .= "GROUP BY mood";

		$result = array();
		$out = '';
		$result['data'] = $this->db->readRows($query);

		$out = array();
		$total = count($result['data']);
		foreach($result['data'] as $data) {
			$moodPercent = $data['countMood'] / $total * 100;
			$out[] = array($mood[$data['mood']], $moodPercent);
		}

		echo json_encode($out);
	}

	/**
	 * collects all Data for displaying site
	 *
	 * @param array $data
	 */
	protected function getChartDataMoodHistoryAction(array $data)
	{
		$todaysDate = $this->helper->getTodaysDate();
		$data = $this->db->validateData($data);

		// Constrain: History for all, but splitted
		$addToQuery = '';
		if ($data['mode'] === self::CHART_MODE_HISTORY_ALL_SPLITTED) {
			$addToQuery = ', user ';
		}

		// Select average overall given moods for today ceiled
		$query =  "SELECT CEIL(avg(mood)) AS averageMood, date " . $addToQuery;
		$query .= "FROM data ";

		// Constrain: History only for one (current) user:
	    if ($data['mode'] === self::CHART_MODE_HISTORY_OWN) {
		    $query .= "WHERE user = '" . $data['user'] . "'";
	    }

		$query .= "GROUP BY date " . $addToQuery;
		$query .= "ORDER BY date ";
		$query .= "Limit 0,30";
		$result['data'] = ($this->db->readRows($query));

		$out = array();
		foreach($result['data'] as $row) {

			if (isset($row['user']) === false) {
				$row['user'] = 'all';
			}

			// Try this, to draw data lines with labels with data:
			//$out[$row['user']][] = array( (string)$row['date'], (int)$row['averageMood']);

			$out[$row['user']][] = (int)$row['averageMood'];
		}

		if ($data['mode'] != self::CHART_MODE_HISTORY_ALL_SPLITTED) {
			$out = $out['all'];
		}

		echo json_encode($out);
	}

	/**
	 * collects all Data for displaying site
	 *
	 * @param array $data
	 */
	protected function getPageDataAction(array $data)
	{
		$data = $this->db->validateData($data);

		$query = "SELECT * FROM data WHERE user = '{$data['user']}'
					AND date >= DATE_SUB(NOW(), INTERVAL 1 DAY)
					ORDER BY date DESC
					LIMIT 1
				";

		$result['user'] =  $this->db->read($query);

		$todaysDate = $this->helper->getTodaysDate();

		// Select average ofall given moods for today ceiled
		$query = "SELECT * FROM (
						SELECT  * FROM data
								  WHERE date >= DATE_SUB(NOW(), INTERVAL 1 DAY)
								  ORDER BY  date DESC
				 ) AS temp GROUP BY USER
		";

		$rows = $this->db->readRows($query);
		// calculate average mood
		$moodSummed = 0;
		foreach ($rows as $row) {
			$moodSummed += $row['mood'];
		}

		$data = array(
			'averageMood' => ceil(($moodSummed / count($rows))),
			'count' => count($rows)
		);

		$result['data'] = $data;

		echo json_encode($result);
	}
}