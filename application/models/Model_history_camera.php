<?php
require 'vendor/autoload.php';

use Aws\DynamoDb\DynamoDbClient;
use Aws\Exception\AwsException;


class Model_history_camera extends Base_model
{
	protected $table;
	public function __construct()
	{
		parent::__construct();
		$this->table = "tbl_history_camera";
		if (defined('AWS_ACCESS_KEY_ID')) {
			$this->client = new DynamoDbClient([
				'region' => 'us-east-2',
				'version' => 'latest',
				'credentials' => [
					'key' => AWS_ACCESS_KEY_ID,
					'secret' => AWS_SECRET_ACCESS_KEY,
				],
			]);
		} else {
			$this->client = new DynamoDbClient([
				'region' => 'us-east-2',
				'version' => 'latest',
			]);
		}
	}

	public function getById($arg)
	{
		$this->db->select('*');
		$this->db->where('id', $arg);
		$query = $this->db->get($this->table);
		return $query->row_array();
	}

	public function deleteById($id)
	{
		$this->db->where("id", $id);
		$this->db->delete($this->table);
	}

	// public function replaceData($data)
	// {
	// 	return $this->db->replace($this->table, $data);
	// }

	public function replaceData($data)
	{
		if (!isset($data['IMEI']) || empty($data['IMEI'])) {
			throw new Exception("IMEI is required and cannot be empty.");
		}

		$IMEI = !empty($data['IMEI']) ? (string) $data['IMEI'] : '';
		$user_id = isset($data['user_id']) && is_numeric($data['user_id']) ? (string) $data['user_id'] : "0";
		$user_email = !empty($data['user_email']) ? (string) $data['user_email'] : '';
		$user_name = !empty($data['user_name']) ? (string) $data['user_name'] : '';
		$info = !empty($data['info']) ? (string) $data['info'] : '';
		$created_at = isset($data['created_at']) && !empty($data['created_at'])
			? (string) strtotime($data['created_at'])
			: (string) time();

		$item = [
			'IMEI' => ['S' => $IMEI],
			'user_id' => ['N' => $user_id],
			'user_email' => ['S' => $user_email],
			'user_name' => ['S' => $user_name],
			'info' => ['S' => $info],
			'created_at' => ['N' => (string) $created_at],
			'pk_all' => ['S' => 'All']
		];

		try {
			$result = $this->client->putItem([
				'TableName' => $this->table,
				'Item' => $item,
			]);
		} catch (AwsException $e) {
		}
	}

	// public function getTotalCount($filterOpt)
	// {
	// 	$sql = "SELECT count(id) as count FROM $this->table WHERE ";
	// 	$sql .= "IMEI LIKE '%" . $filterOpt["search"] . "%' OR ";
	// 	$sql .= "info LIKE '%" . $filterOpt["search"] . "%' OR ";
	// 	$sql .= "user_name LIKE '%" . $filterOpt["search"] . "%' OR ";
	// 	$sql .= "user_email LIKE '%" . $filterOpt["search"] . "%' ";
	// 	$query = $this->db->query($sql);
	// 	$result = $query->row_array();
	// 	return  $result['count'];
	// }

	// public function getSearchData($filterOpt)
	// {
	// 	$sql = 'SELECT *, UNIX_TIMESTAMP(created_at) as created_at FROM ' . $this->table . ' WHERE ';
	// 	$sql .= "IMEI LIKE '%" . $filterOpt["search"] . "%' OR ";
	// 	$sql .= "info LIKE '%" . $filterOpt["search"] . "%' OR ";
	// 	$sql .= "user_name LIKE '%" . $filterOpt["search"] . "%' OR ";
	// 	$sql .= "user_email LIKE '%" . $filterOpt["search"] . "%' ";
	// 	$sql .= "ORDER BY " . $filterOpt['order_field'] . " " . $filterOpt['order_dir'] . " ";
	// 	$sql .= "LIMIT " . $filterOpt['length'] . " OFFSET " . $filterOpt['start'];
	// 	$query = $this->db->query($sql);
	// 	$result = $query->result_array();
	// 	return $result;
	// }

	public function getTotalCount($filterOpt)
	{
		$IMEI = isset($filterOpt['IMEI']) ? $filterOpt['IMEI'] : $filterOpt['search'];
		try {
			if (strlen($IMEI) === 0) {
				$result = $this->client->scan([
					'TableName' => $this->table,
					'Select' => 'COUNT',
				]);
			} else {
				$result = $this->client->query([
					'TableName' => $this->table,
					'KeyConditionExpression' => 'IMEI = :imei',
					'ExpressionAttributeValues' => [
						':imei' => ['S' => $IMEI]
					],
					'Select' => 'COUNT'
				]);
			}
			return (int) $result['Count'];
		} catch (DynamoDbException $e) {
			error_log("Unable to count items: " . $e->getMessage());
			return 0;
		}
	}

	public function getSearchData($filterOpt)
	{
		$IMEI = isset($filterOpt['IMEI']) ? $filterOpt['IMEI'] : $filterOpt['search'];
		$limit = isset($filterOpt['length']) ? (int) $filterOpt['length'] : 10;
		$offset = isset($filterOpt['start']) ? (int) $filterOpt['start'] : 0;
		$order_by = $filterOpt['order_dir'];
		$lastEvaluatedKey = !empty($filterOpt['lastEvaluatedKey']) ? $filterOpt['lastEvaluatedKey'] : null;
		$items = [];
		if (strlen($IMEI) === 0 || $IMEI === null || $IMEI === "" || empty($IMEI)) {
			$params = [
				'TableName' => $this->table,
				'IndexName' => 'pk_all-created_at-index',
				'KeyConditionExpression' => 'pk_all = :pk_all',
				'ExpressionAttributeValues' => [
					':pk_all' => ['S' => 'All'],
				],
				'ScanIndexForward' => false,
				'Limit' => $limit,
			];
		} else {
			$params = [
				'TableName' => $this->table,
				'KeyConditionExpression' => 'IMEI = :IMEI',
				'ExpressionAttributeValues' => [
					':IMEI' => ['S' => $IMEI]
				],
				'ScanIndexForward' => false,
				'Limit' => $limit
			];
		}
		if ($order_by === 'asc') {
			$params['ScanIndexForward'] = true;
		}
		if ($lastEvaluatedKey !== null) {
			$params['ExclusiveStartKey'] = $lastEvaluatedKey;
		}
		try {
			$result = $this->client->query($params);
			$items = $result['Items'];
			$lastEvaluatedKey = $result['LastEvaluatedKey'] ?? null;
		} catch (AwsException $e) {
			echo "Error: " . $e->getMessage() . "\n";
		}
		if (empty($items)) {
			return [];
		}
		return array_map(function ($item) use ($lastEvaluatedKey) {
			return [
				'IMEI' => $item['IMEI']['S'],
				'user_id' => isset($item['user_id']) ? (int) $item['user_id']['N'] : 0,
				'user_email' => $item['user_email']['S'],
				'user_name' => $item['user_name']['S'],
				'info' => $item['info']['S'],
				'created_at' => (int) $item['created_at']['N'],
				'lastEvaluatedKey' => $lastEvaluatedKey ?? null,
			];
		}, $items);
	}

	public function getTotalCountIMEI($filterOpt)
	{
		$sql = "SELECT count(id) as count FROM $this->table WHERE ";
		$sql .= "IMEI = '" . $filterOpt["IMEI"] . "' ";
		$query = $this->db->query($sql);
		$result = $query->row_array();
		return $result['count'];
	}

	public function getSearchDataIMEI($filterOpt)
	{
		$sql = 'SELECT *, UNIX_TIMESTAMP(created_at) as created_at FROM ' . $this->table . ' WHERE ';
		$sql .= "IMEI = '" . $filterOpt["IMEI"] . "' ";
		$sql .= "ORDER BY " . $filterOpt['order_field'] . " " . $filterOpt['order_dir'] . " ";
		$sql .= "LIMIT " . $filterOpt['length'] . " OFFSET " . $filterOpt['start'];
		$query = $this->db->query($sql);
		$result = $query->result_array();
		return $result;
	}
}
