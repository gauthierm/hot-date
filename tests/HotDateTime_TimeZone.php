
<?php

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'HotDate/HotDateTime.php';
require_once 'HotDate/HotDateTimeZone.php';

class HotDateTime_TimeZone extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		date_default_timezone_set('Europe/Budapest');
	}

	public function testDefaultTimeZone()
	{
		$date = new HotDateTime('2010-07-01');
		$this->assertEquals(
			'Europe/Budapest',
			$date->getTimezone()->getName(),
			'Default time-zone not set correctly on new HotDateTime.'
		);
	}

	public function testGetTimezone()
	{
		$date = new HotDateTime(
			'2010-07-01',
			new HotDateTimeZone('America/Toronto')
		);

		$this->assertEquals(
			'America/Toronto',
			$date->getTimezone()->getName(),
			'getTimezone returned unexpected time-zone.'
		);
	}

	public function testSetTimezone()
	{
		$date = new HotDateTime('2010-07-01');
		$date->setTimezone(new HotDateTimeZone('America/Halifax'));

		$this->assertEquals(
			'America/Halifax',
			$date->getTimezone()->getName(),
			'setTimezone did not actually set new time-zone.'
		);

		$this->assertEquals(
			19,
			$date->getHour(),
			'setTimezone did not properly update the HotDateTime hours.'
		);
	}

	public function testConvertTZ()
	{
		$date = new HotDateTime('2010-07-01');
		$date->setTimezone(new HotDateTimeZone('America/Halifax'));

		$this->assertEquals(
			'America/Halifax',
			$date->getTimezone()->getName(),
			'convertTZ did not actually set new time-zone.'
		);

		$this->assertEquals(
			19,
			$date->getHour(),
			'convertTZ did not properly update the HotDateTime hours.'
		);
	}

	public function testConvertTZById()
	{
		$date = new HotDateTime('2010-07-01');
		$date->convertTZById('America/Halifax');

		$this->assertEquals(
			'America/Halifax',
			$date->getTimezone()->getName(),
			'convertTZById did not actually set new time-zone.'
		);

		$this->assertEquals(
			19,
			$date->getHour(),
			'convertTZById did not properly update the HotDateTime hours.'
		);
	}

	public function testSetTZ()
	{
		$date = new HotDateTime('2010-07-01');
		$date->setTZ(new HotDateTimeZone('America/Halifax'));

		$this->assertEquals(
			'America/Halifax',
			$date->getTimezone()->getName(),
			'setTZ did not actually set new time-zone.'
		);

		$this->assertEquals(
			0,
			$date->getHour(),
			'setTZ was not supposed to update the hours.'
		);
	}

	public function testSetTZById()
	{
		$date = new HotDateTime('2010-07-01');
		$date->setTZById('America/Halifax');

		$this->assertEquals(
			'America/Halifax',
			$date->getTimezone()->getName(),
			'setTZById did not actually set new time-zone.'
		);

		$this->assertEquals(
			0,
			$date->getHour(),
			'setTZById was not supposed to update the hours.'
		);
	}
}

?>
