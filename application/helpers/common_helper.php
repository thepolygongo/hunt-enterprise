<?php
/**
 * Translate keyowrd with current language from session
 */
function translate( $keyword, $module = 'user' ) {
	return $keyword;
}

/**
 * Functions of Dates 
 */

function generateRandomString( $length = 10 ) {
	$chars = array_merge(range('A', 'Z'), range(0, 9));
	shuffle($chars);
	return implode(array_slice($chars, 0, $length));
}

function generateRandomNumber( $length = 10 ) {
	$chars = array_merge(range(0, 9));
	shuffle($chars);
	return implode(array_slice($chars, 0, $length));
}

function parse_sku($SKU) {
	$strArray = explode("-", $SKU);
	if(is_array($strArray) && sizeof($strArray) == 3) {
		$sub_string = "";
		$sub_count = 0;
		$boost_count = 0;
		$qunatity = 0;
		if ($strArray[0] == "ww") {
			$sub_string = "Weekend Warrior";
			$sub_count = 500 * 1000000;
			$boost_count = 150 * 1000000;
			$qunatity = (int)$strArray[2];
		} else if ($strArray[0] == "th") {
			$sub_string = "Trophy Hunter";
			$sub_count = 1000 * 1000000;
			$boost_count = 250 * 1000000;
			$qunatity = (int)$strArray[2];
		} else if ($strArray[0] == "b") {
			$sub_string = "Base";
			$sub_count = 100 * 1000000;
			$boost_count = 100 * 1000000;
			$qunatity = (int)$strArray[2];
		} else if ($strArray[0] == "sd") {
			$sub_string = "SD";
			$sub_count = 0;
			$boost_count = 0;
			$qunatity = 0;
		}

		$period_string = $strArray[1] == 'm' ? "Monthly" : "Yearly";

		return array(
			'sub_string' => $sub_string,
			'period_string' => $period_string,
			'sub_count' => $sub_count,
			'boost_count' => $boost_count,
			'plan' => $strArray[0],
			'period' => $strArray[1],
			'quantity' => $qunatity,
		);
	} else {
		return array(
			'sub_string' => 'Wrong SKU',
			'period_string' => '',
			'sub_count' => 0,
			'boost_count' => 0,
			'plan' => '',
			'period' => '',
			'quantity' => 0,
		);
	}
}