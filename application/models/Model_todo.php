<?php

require 'vendor/autoload.php';

use Aws\DynamoDb\DynamoDbClient;
use Aws\Exception\AwsException;




class Model_todo extends Base_model
{
	public function __construct()
	{
		parent::__construct();
		$this->table = "tbl_todo";
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

	public function get_by_id_from_sql($id)
	{
		$this->db->select('*');
		$this->db->where('id', $id);
		$query = $this->db->get($this->table);
		return $query->row_array();
	}

	public function get_50($start, $length)
	{
		$sql = "SELECT * FROM $this->table WHERE 1 ORDER BY id ASC LIMIT $length OFFSET $start";
		$query = $this->db->query($sql);
		$result = $query->result_array();
		return $result;
	}

	public function get_item_count()
	{
		try {
			$result = $this->client->scan([
				'TableName' => $this->table,
				'Select' => 'COUNT',
			]);

			$count = $result['Count'];

			return $count;
		} catch (AwsException $e) {
			echo "Error: " . $e->getMessage() . "\n";
			return null;
		}
	}

	public function insert_to_dynamodb($data)
	{
		$note = isset($data['note']) && !empty($data['note']) ? $data['note'] : '';
		$by_admin = isset($data['by_admin']) && !empty($data['by_admin']) ? $data['by_admin'] : '';
		$checked = isset($data['checked']) ? (bool) $data['checked'] : false;

		$created_at = isset($data['created_at']) && !empty($data['created_at'])
			? strtotime($data['created_at'])
			: time();

		if (!isset($data['user_id']) || empty($data['user_id'])) {
			return;
		}

		$item = [
			'user_id' => ['N' => (string) (int) $data['user_id']],
			'note' => ['S' => $note],
			'by_admin' => ['S' => $by_admin],
			'checked' => ['BOOL' => $checked],
			'created_at' => ['N' => (string) $created_at]
		];

		try {
			$result = $this->client->putItem([
				'TableName' => $this->table,
				'Item' => $item,
			]);
		} catch (AwsException $e) {
			echo "Error adding item: " . $e->getMessage() . "\n";
		}
	}


	// public function replaceData($data)
	// {
	// 	$this->db->replace($this->table, $data);
	// 	return $this->db->insert_id();
	// }

	// public function getAllByUser($user_id)
	// {
	// 	$sql = 'SELECT *, UNIX_TIMESTAMP(created_at) as created_at FROM ' . $this->table . ' WHERE ';
	// 	$sql .= "user_id=$user_id ";
	// 	$sql .= "ORDER BY created_at DESC";
	// 	$query = $this->db->query($sql);
	// 	return $query->result_array();
	// }


	// public function deleteById($id)
	// {
	// 	$this->db->where("id", $id);
	// 	$this->db->delete($this->table);
	// }

	public function getById($user_id, $created_at)
	{
		$key = [
			'user_id' => ['N' => $user_id],
			'created_at' => ['N' => $created_at]
		];

		try {
			$result = $this->client->getItem([
				'TableName' => $this->table,
				'Key' => $key
			]);

			if (isset($result['Item'])) {
				echo "Item retrieved: \n";
				return [
					'user_id' => $result['Item']['user_id']['N'],
					'note' => $result['Item']['note']['S'],
					'by_admin' => $result['Item']['by_admin']['S'],
					'checked' => $result['Item']['checked']['BOOL'],
					'created_at' => $result['Item']['created_at']['N'],
				];
			} else {
				echo "Item not found.\n";
			}
		} catch (AwsException $e) {
			echo "Error retrieving item: " . $e->getMessage() . "\n";
		}
	}

	public function replaceData($data)
	{
		$note = isset($data['note']) && !empty($data['note']) ? $data['note'] : '';
		$by_admin = isset($data['by_admin']) && !empty($data['by_admin']) ? $data['by_admin'] : '';
		$checked = isset($data['checked']) ? (bool) $data['checked'] : false;
		$created_at = isset($data['created_at']) && !empty($data['created_at']) ? $data['created_at'] : (string) time();

		if (!isset($data['user_id']) || empty($data['user_id'])) {
			echo "Error: 'user_id' is missing or empty.";
			return;
		}

		$item = [
			'user_id' => ['N' => $data['user_id']],
			'note' => ['S' => $note],
			'by_admin' => ['S' => $by_admin],
			'checked' => ['BOOL' => $checked],
			'created_at' => ['N' => $created_at]
		];

		try {
			$result = $this->client->putItem([
				'TableName' => $this->table,
				'Item' => $item,
			]);
		} catch (AwsException $e) {
		}
	}

	public function getAllByUser($user_id)
	{
		try {
			$result = $this->client->query([
				'TableName' => $this->table,
				'KeyConditionExpression' => 'user_id = :user_id',
				'ExpressionAttributeValues' => [
					':user_id' => ['N' => (string) $user_id],
				],
			]);

			$items = $result['Items'];
			return array_map(function ($item) {
				return [
					'user_id' => $item['user_id']['N'],
					'note' => $item['note']['S'],
					'by_admin' => $item['by_admin']['S'],
					'checked' => $item['checked']['BOOL'],
					'created_at' => $item['created_at']['N'],
				];
			}, $items);

		} catch (AwsException $e) {
			echo "Error querying items: " . $e->getMessage() . "\n";
			return [];
		}
	}



	public function deleteById($user_id, $created_at)
	{
		$key = [
			'user_id' => ['N' => $user_id],
			'created_at' => ['N' => $created_at]
		];

		try {
			$result = $this->client->deleteItem([
				'TableName' => $this->table,
				'Key' => $key
			]);
			echo "Item deleted successfully.\n";
		} catch (AwsException $e) {
			echo "Error deleting item: " . $e->getMessage() . "\n";
		}
	}
}
