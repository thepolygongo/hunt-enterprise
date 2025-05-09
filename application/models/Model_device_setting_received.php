<?php
require 'vendor/autoload.php';

use Aws\DynamoDb\DynamoDbClient;
use Aws\Exception\AwsException;

class Model_device_setting_received extends Base_model
{
	protected $table;

	public function __construct()
	{
		parent::__construct();
		$this->table = "tbl_device_setting_received";
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

	public function getByIMEI($IMEI)
	{
		$params = [
			'TableName' => $this->table,
			'KeyConditionExpression' => 'IMEI = :imei',
			'ExpressionAttributeValues' => [
				':imei' => ['S' => $IMEI]
			],
			'ScanIndexForward' => false
		];

		try {
			$result = $this->client->query($params);
			$items = $result['Items'];

			if (empty($items)) {
				return [];
			}
			$item = $items[0];
			$data = [];
			foreach ($item as $key => $value) {
				$data[$key] = $item[$key]['S'] ?? $item[$key]['N'];
			}

			return $data;
		} catch (DynamoDbException $e) {
			error_log("DynamoDB Error: " . $e->getMessage());
			return [];
		}
	}

	public function protocol($IMEI)
	{
		$setting = $this->getByIMEI($IMEI);
		if (!is_array($setting) || empty($setting)) {
			return -1;
		}
		$connect = $setting['protocol_type'];
		return $connect;
	}
}
