<?php
/**
*
* This file is part of the phpBB Forum Software package.
*
* @copyright (c) phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
* For full copyright and license information, please see
* the docs/CREDITS.txt file.
*
*/

require_once dirname(__FILE__) . '/../mock/lang.php';

class phpbb_datetime_from_format_test extends phpbb_test_case
{
	public function from_format_data()
	{
		return array(
			array(
				'UTC',
				'Y-m-d',
				'2012-06-08',
			),

			array(
				'Europe/Berlin',
				'Y-m-d H:i:s',
				'2012-06-08 14:01:02',
			),
		);
	}

	/**
	* @dataProvider from_format_data()
	*/
	public function test_from_format($timezone, $format, $expected)
	{
		$user = new \phpbb\user('\phpbb\datetime');
		$user->timezone = new DateTimeZone($timezone);
		$user->lang['datetime'] = array(
			'TODAY'		=> 'Today',
			'TOMORROW'	=> 'Tomorrow',
			'YESTERDAY'	=> 'Yesterday',
			'AGO'		=> array(
				0		=> 'less than a minute ago',
				1		=> '%d minute ago',
				2		=> '%d minutes ago',
			),
		);

		$timestamp = $user->get_timestamp_from_format($format, $expected, new DateTimeZone($timezone));
		$this->assertEquals($expected, $user->format_date($timestamp, $format, true));
	}


	public function relative_format_date_data()
	{
		// If the current time is too close to the testing time,
		// the relative time will use "x minutes ago" instead of "today ..."
		// So we use 18:01 in the morning and 06:01 in the afternoon.
		$testing_time = date('H') <= 12 ? '06:01' : '18:01';

		return array(
			array(
				date('Y-m-d', time() + 2 * 86400) . ' ' . $testing_time, false,
				date('Y-m-d', time() + 2 * 86400) . ' ' . $testing_time,
			),

			array(
				date('Y-m-d', time() + 86400) . ' ' . $testing_time, false,
				'Tomorrow ' . $testing_time,
			),
			array(
				date('Y-m-d', time() + 86400) . ' ' . $testing_time, true,
				date('Y-m-d', time() + 86400) . ' ' . $testing_time,
			),

			array(
				date('Y-m-d') . ' ' . $testing_time, false,
				'Today ' . $testing_time,
			),
			array(
				date('Y-m-d') . ' ' . $testing_time, true,
				date('Y-m-d') . ' ' . $testing_time,
			),

			array(
				date('Y-m-d', time() - 86400) . ' ' . $testing_time, false,
				'Yesterday ' . $testing_time,
			),
			array(
				date('Y-m-d', time() - 86400) . ' ' . $testing_time, true,
				date('Y-m-d', time() - 86400) . ' ' . $testing_time,
			),

			array(
				date('Y-m-d', time() - 2 * 86400) . ' ' . $testing_time, false,
				date('Y-m-d', time() - 2 * 86400) . ' ' . $testing_time,
			),
		);
	}

	/**
	 * @dataProvider relative_format_date_data()
	 */
	public function test_relative_format_date($timestamp, $forcedate, $expected)
	{
		$user = new \phpbb\user('\phpbb\datetime');
		$user->timezone = new DateTimeZone('UTC');
		$user->lang['datetime'] = array(
			'TODAY'		=> 'Today',
			'TOMORROW'	=> 'Tomorrow',
			'YESTERDAY'	=> 'Yesterday',
			'AGO'		=> array(
				0		=> 'less than a minute ago',
				1		=> '%d minute ago',
				2		=> '%d minutes ago',
			),
		);

		$timestamp = $user->get_timestamp_from_format('Y-m-d H:i', $timestamp, new DateTimeZone('UTC'));
		$this->assertEquals($expected, $user->format_date($timestamp, '|Y-m-d| H:i', $forcedate));
	}
}
