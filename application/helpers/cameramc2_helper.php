<?php

function dc2bOptionLabel($key, $value)
{
	$mc2Options = array(
		'mode' => [
			'',
			'Picture (default)',
			'Video',
			'Pic + Video'
		],
		'pic_size' => [
			'',
			'5MP',
			'8MP',
			'12MP'
		],
		'multi_shot' => [
			'',
			'1 Picture',
			'2 Pictures',
			'3 Pictures'
		],
		'burst_interval' => [
			'',
			'1s'
		],
		'video_size' => [
			'',
			'WVGA',
			'720P',
			'1080P'
		],
		'video_length' => [
			'',
			'5s',
			'10s'
		],
		'video_fps' => [
			'',
			'15FPS',
			'30FPS'
		],
		'night_vision' => [
			'',
			'Min Blur',
		],
		'flash_distance' => [
			'',
			'Near',
			'Far',
		],
		'remote_ctrl' => [
			'4 Times Daily',
			'Always On',
		],
		'frequency' => [
			'Immediately',
			'Every 1H',
			'Every 4H',
		],
		'transpic' => [
			'OFF',
			'ON',
		],
		'transvideo' => [
			'OFF',
			'Full Video',
			'Thumbnail Files',
		],
		'battery_type' => [
			'Alkaline',
			'Ni-MH',
		],
		'sd_cycle' => [
			'OFF',
			'ON',
		],
		'pir_switch' => [
			'ON',
			'OFF',
		],
		'gps_on' => [
			'OFF',
			'ON',
		],
		'verizon_priority' => [
			'OFF',
			'ON',
		],
	);

	if (key_exists($key, $mc2Options)) {
		$labels = $mc2Options[$key];
		if (sizeof($labels) > $value)
			return $labels[$value];
	} else {
		if (in_array($key, array('name', 'delay_time', 'pir_interval', 'work_timer_1', 'work_timer_2', 'motion_sensitivity', 'max_num'))) {
			return $value;
		}
	}
	return '-null-';
};

function mc2OptionLabel($key, $value)
{
	$mc2Options = array(
		'mode' => [
			'',
			'Picture (default)',
			'Video',
			'Pic + Video'
		],
		'pic_size' => [
			'',
			'5MP',
			'8MP',
			'12MP'
		],
		'multi_shot' => [
			'',
			'1 Picture',
			'2 Pictures',
			'3 Pictures'
		],
		'burst_interval' => [
			'',
			'1s'
		],
		'video_size' => [
			'',
			'WVGA',
			'720P',
			'1080P'
		],
		'video_length' => [
			'',
			'5s',
			'10s'
		],
		'video_fps' => [
			'',
			'15FPS',
			'30FPS'
		],
		'night_vision' => [
			'',
			'Min Blur',
		],
		'flash_distance' => [
			'',
			'Near',
			'Far',
		],
		'remote_ctrl' => [
			'4 Times Daily',
			'Always On',
		],
		'frequency' => [
			'Immediately',
			'Every 1H',
			'Every 4H',
		],
		'transpic' => [
			'OFF',
			'ON',
		],
		'transvideo' => [
			'OFF',
			'Full Video',
			'Thumbnail Files',
		],
		'battery_type' => [
			'Alkaline',
			'Ni-MH',
		],
		'sd_cycle' => [
			'OFF',
			'ON',
		],
		'pir_switch' => [
			'ON',
			'OFF',
		],
	);

	if (key_exists($key, $mc2Options)) {
		$labels = $mc2Options[$key];
		if (sizeof($labels) > $value)
			return $labels[$value];
	} else {
		if (in_array($key, array('name', 'delay_time', 'pir_interval', 'work_timer_1', 'work_timer_2', 'motion_sensitivity', 'max_num'))) {
			return $value;
		}
	}
	return '-null-';
};
