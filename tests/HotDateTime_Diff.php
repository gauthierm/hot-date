<?php

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'HotDate/HotDateTime.php';
require_once 'HotDate/HotDateInterval.php';

class HotDateTime_Diff extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		date_default_timezone_set('UTC');
	}

	public function testInvert()
	{
		$date1 = new HotDateTime('2010-01-01T00:00:00');
		$date2 = new HotDateTime('2010-01-01T00:00:01');

		$interval = $date1->diff($date2);
		$this->assertFalse(
			$interval->invert,
			'Subtracting greater date from lesser date results in incorrect ' .
			'interval inversion.'
		);

		$interval = $date2->diff($date1);
		$this->assertTrue(
			$interval->invert,
			'Subtracting lesser date from greater date results in incorrect ' .
			'interval inversion.'
		);

		$interval = $date1->diff($date2, true);
		$this->assertFalse(
			$interval->invert,
			'Subtracting greater date from lesser date using absolute ' .
			'parameter results in incorrect interval inversion.'
		);

		$interval = $date2->diff($date1, true);
		$this->assertFalse(
			$interval->invert,
			'Subtracting lesser date from greater date using absolute ' .
			'parameter results in incorrect interval inversion.'
		);
	}

	public function testSeconds()
	{
		$date1 = new HotDateTime('2010-01-01T00:00:00');
		$date2 = new HotDateTime('2010-01-01T00:00:01');

		$expectedInterval = new HotDateInterval('PT1S');
		$expectedInterval->days = 0;
		$expectedInterval->invert = false;
		$interval = $date1->diff($date2);
		$this->assertEquals(
			$expectedInterval,
			$interval,
			'Subtracting greater date from lesser date results in incorrect ' .
			'number of seconds.'
		);

		$expectedInterval = new HotDateInterval('PT1S');
		$expectedInterval->days = 0;
		$expectedInterval->invert = true;
		$interval = $date2->diff($date1);
		$this->assertEquals(
			$expectedInterval,
			$interval,
			'Subtracting lesser date from greater date results in incorrect ' .
			'number of seconds.'
		);
	}

	public function testMinutes()
	{
		$date1 = new HotDateTime('2010-01-01T00:00:00');
		$date2 = new HotDateTime('2010-01-01T00:01:00');

		$expectedInterval = new HotDateInterval('PT1M');
		$expectedInterval->days = 0;
		$expectedInterval->invert = false;
		$interval = $date1->diff($date2);
		$this->assertEquals(
			$expectedInterval,
			$interval,
			'Subtracting greater date from lesser date results in incorrect ' .
			'number of minutes.'
		);

		$expectedInterval = new HotDateInterval('PT1M');
		$expectedInterval->days = 0;
		$expectedInterval->invert = true;
		$interval = $date2->diff($date1);
		$this->assertEquals(
			$expectedInterval,
			$interval,
			'Subtracting lesser date from greater date results in incorrect ' .
			'number of seconds or incorrect interval.'
		);
	}

	public function testZero()
	{
		$date1 = new HotDateTime('2010-01-01T00:00:00');
		$date2 = new HotDateTime('2010-01-01T00:00:00');

		$expectedInterval = new HotDateInterval('P');
		$expectedInterval->days = 0;
		$expectedInterval->invert = false;
		$interval = $date1->diff($date2);
		$this->assertEquals(
			$expectedInterval,
			$interval,
			'Subtracting equivalent dates doesn\'t result in zero interval.'
		);
	}

	public function testWrap()
	{
		$date1 = new HotDateTime('2010-01-01T00:00:00');
		$date2 = new HotDateTime('2009-01-01T00:00:01');

		$expectedInterval = new HotDateInterval('P11M30DT23H59M59S');
		$expectedInterval->days = 364;
		$expectedInterval->invert = true;
		$interval = $date1->diff($date2);
		$this->assertEquals(
			$expectedInterval,
			$interval,
			'Subtracting equivalent dates doesn\'t result in zero interval.'
		);
	}
}

?>
