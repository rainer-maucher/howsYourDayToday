<?php
/**
 * Class that dispatches Actions by given Data
 *
 * Class Lib_Dispatcher
 */
class Lib_Dispatcher {
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
		$date = $this->helper->getTodaysDate();
		$data = $this->db->validateData($data);
		$query = "INSERT INTO data (user, date, mood) VALUES ('{$data['user']}', '$date', {$data['mood']}) ON DUPLICATE KEY UPDATE mood={$data['mood']}";

		$this->db->query($query);
	}

	/**
	 * collects all Data for displaying site
	 *
	 * @param array $data
	 */
	protected function getPageDataAction(array $data)
	{
		$todaysDate = $this->helper->getTodaysDate();
		$data = $this->db->validateData($data);

		$query = "SELECT * FROM data WHERE user = '{$data['user']}' AND date = '{$todaysDate}'";
		$result['user'] =  $this->db->read($query);

		// Select average ofall given moods for today ceiled
		$query = "SELECT CEIL(avg(mood)) AS averageMood, COUNT(*) AS count  FROM data WHERE  date = '{$todaysDate}'";
		$result['data'] = ($this->db->read($query));

		echo json_encode($result);
	}
}