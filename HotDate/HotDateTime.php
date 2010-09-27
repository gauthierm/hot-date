<?php

require_once 'HotDate/HotDateInterval.php';
require_once 'HotDate/HotDatePeriod.php';
require_once 'HotDate/HotDateTimeZone.php';

/**
 * PHP 5.2 to 5.3 adapter class for DateTime
 *
 * Supports the PHP 5.3 API as well as serialization.
 */
class HotDateTime implements Serializable
{
	protected $dateTime = null;
	protected $timeZone = null;

	public function __construct($time = 'now', DateTimeZone $timeZone = null)
	{
		if ($timeZone === null) {
			$timeZone = new HotDateTimeZone(date_default_timezone_get());
		}

		if (!($timeZone instanceof HotDateTimeZone)) {
			$timeZone = new HotDateTimeZone($timeZone->getName());
		}

		$this->dateTime = new DateTime($time, $timeZone);
		$this->timeZone = $timeZone;
	}

	public function add(HotDateInterval $interval)
	{
		$modify = '';

		if ($interval->days) {
			$sign = ($interval->days > 0) ? '+' : '-';
			$modify .= ' ' . $sign . abs($interval->days) . ' days';
		} else {
			if ($interval->y) {
				$sign = ($interval->y > 0) ? '+' : '-';
				$modify .= ' ' . $sign . abs($interval->y) . ' years';
			}

			if ($interval->m) {
				$sign = ($interval->m > 0) ? '+' : '-';
				$modify .= ' ' . $sign . abs($interval->m) . ' months';
			}

			if ($interval->d) {
				$sign = ($interval->d > 0) ? '+' : '-';
				$modify .= ' ' . $sign . abs($interval->d) . ' days';
			}

			if ($interval->h) {
				$sign = ($interval->h > 0) ? '+' : '-';
				$modify .= ' ' . $sign . abs($interval->h) . ' hours';
			}

			if ($interval->i) {
				$sign = ($interval->i > 0) ? '+' : '-';
				$modify .= ' ' . $sign . abs($interval->i) . ' minutes';
			}

			if ($interval->s) {
				$sign = ($interval->s > 0) ? '+' : '-';
				$modify .= ' ' . $sign . abs($interval->s) . ' seconds';
			}
		}

		if ($interval->invert) {
			$modify = str_replace('-', '*', $modify);
			$modify = str_replace('+', '-', $modify);
			$modify = str_replace('*', '+', $modify);
		}

		$modify = trim($modify);

		if ($modify) {
			return $this->modify($modify);
		}

		return $this;
	}

	public function diff(HotDateTime $datetime2, $absolute = false)
	{
		$seconds1 = $this->getTimestamp();
		$seconds2 = $datetime2->getTimestamp();

		// The following calculations assume a lesser date is being subtracted
		// from a greater date. Set variables appropriately.
		if ($seconds1 > $seconds2) {
			$end   = clone $this;
			$start = clone $datetime2;
		} else {
			$end   = clone $datetime2;
			$start = clone $this;
		}

		// Convert to UTC so time zones will be considered. Note: PHP 5.3
		// does not do this correctly. See http://bugs.php.net/bug.php?id=52480
		$start->setTimezone(new HotDateTimeZone('UTC'));
		$end->setTimezone(new HotDateTimeZone('UTC'));

		// Calculate seconds, minutes and hours.
		$end->sub(
			new HotDateInterval(
				  'PT' . $start->format('H\Hi\Ms\S')
			)
		);

		// Can't use HotDateTime::sub() here because it will always wrap
		// months and years to the previous month/year even if the days and
		// months should evaluate to 0.

		// days
		if ($start->format('j') === $end->format('j')) {
			$days = 0;
		} else {
			$end->sub(new HotDateInterval('P' . $start->format('j\D')));
			$days = (integer)$end->format('j');
		}

		// months
		if ($start->format('n') === $end->format('n')) {
			$months = 0;
		} else {
			$end->sub(new HotDateInterval('P' . $start->format('n\M')));
			$months = (integer)$end->format('n');
		}

		// years
		$end->sub(new HotDateInterval('P' . $start->format('Y\Y')));
		$years = (integer)$end->format('Y');

		// Calculate days. This doesn't account for leap-seconds, but PHP 5.3
		// doesn't either.
		$diff = abs($seconds1 - $seconds2);
		$totalDays = (integer)floor($diff / 86400);

		// Create interval result object
		$interval = new HotDateInterval(
			  str_pad($years, 4, '0', STR_PAD_LEFT)
			. str_pad($months, 2, '0', STR_PAD_LEFT)
			. str_pad($days, 2, '0', STR_PAD_LEFT)
			. 'T' . $end->format('His')
		);

		$interval->days = $totalDays;

		if ($seconds1 > $seconds2 && !$absolute) {
			$interval->invert = true;
		}

		return $interval;
	}

	public function format($format)
	{
		return $this->dateTime->format($format);
	}

	public function getOffset()
	{
		return $this->dateTime->getOffset();
	}

	public function getTimestamp()
	{
		return (integer)$this->dateTime->format('U');
	}

	public function getTimezone()
	{
		return $this->timeZone;
	}

	public function modify($modify)
	{
		if ($this->dateTime->modify($modify) === false) {
			return false;
		}
		return $this;
	}

	public function setDate($year, $month, $day)
	{
		if ($this->dateTime->setDate($year, $month, $day) === false) {
			return false;
		}
		return $this;
	}

	public function setISODate($year, $week, $day = 1)
	{
		if ($this->dateTime->setISODate($year, $week, $day) === false) {
			return false;
		}
		return $this;
	}

	public function setTime($hour, $minute, $second)
	{
		if ($this->dateTime->setTime($hour, $minute, $second) === false) {
			return false;
		}
		return $this;
	}

	public function setTimestamp($unixtimestamp)
	{
		$date = new HotDateTime('@' . $unixtimestamp);
		$date->setTimezone($this->getTimezone());

		$result = $this->dateTime->setDate(
			$date->format('Y'),
			$date->format('n'),
			$date->format('j')
		);

		if ($result !== false) {
			$result = $this->dateTime->setTime(
				$date->format('G'),
				$date->format('i'),
				$date->format('s') . '.' . $date->format('u')
			);
		}

		if ($result === false) {
			return false;
		}

		return $this;
	}

	public function setTimezone($timeZone)
	{
		if (!($timeZone instanceof HotDateTimeZone)) {
			if ($timeZone instanceof DateTimeZone) {
				$timeZone = new HotDateTimeZone($timeZone->getName());
			} elseif (is_string($timeZone)) {
				$timeZone = new HotDateTimeZone($timeZone);
			}
		}

		if (!($timeZone instanceof HotDateTimeZone)) {
			throw new InvalidArgumentException('Specified $timeZone is '
			. 'neither a string not a DateTimeZone object.');
		}

		if ($this->dateTime->setTimezone($timeZone) === false) {
			return false;
		}

		$this->timeZone = $timeZone;

		return $this;
	}

	public function sub(HotDateInterval $interval)
	{
		$interval = clone $interval;
		$interval->invert = (!$interval->invert);
		return $this->add($interval);
	}

	public function __toString()
	{
		return $this->format('c')
			. ' (' . $this->getTimezone()->getName() . ')';
	}

	public function serialize()
	{
		$data = array(
			$this->dateTime->format('U'),
			$this->timeZone->getName(),
		);

		return serialize($data);
	}

	public function unserialize($serialized)
	{
		$data = unserialize($serialized);

		$this->timeZone = new HotDateTimeZone($data[1]);
		$this->dateTime = new DateTime('@' . $data[0], $this->timeZone);

		// DateTime constructor with timestamp is always UTC
		$this->dateTime->setTimezone($this->timeZone);
	}

	public function __clone()
	{
		$this->dateTime = clone $this->dateTime;
		$this->timeZone = clone $this->timeZone;
	}

	public function getYear()
	{
		return (integer)$this->format('Y');
	}

	public function getMonth()
	{
		return (integer)$this->format('n');
	}

	public function getDay()
	{
		return (integer)$this->format('j');
	}

	public function getHour()
	{
		return (integer)ltrim($this->format('H'), '0');
	}

	public function getMinute()
	{
		return (integer)ltrim($this->format('i'), '0');
	}

	public function getSecond()
	{
		return (integer)ltrim($this->format('s'), '0');
	}

	public function getDate()
	{
		return $this->format('c');
	}

	public function convertTZ(HotDateTimeZone $timeZone)
	{
		$this->setTimezone($timeZone);
	}

	public function convertTZById($tzId)
	{
		$this->setTimezone(new HotDateTimeZone($tzId));
	}

	public function setTZ(HotDateTimeZone $timeZone)
	{
		$this->addSeconds($this->format('Z'));
		$this->setTimezone($timeZone);
		$this->subtractSeconds($this->format('Z'));
	}

	public function setTZById($tzId)
	{
		$this->setTZ(new HotDateTimeZone($tzId));
	}

	public function toUTC()
	{
		$this->setTimezone(new HotDateTimeZone('UTC'));
	}

	public function getDaysInMonth()
	{
		return (integer)$this->format('t');
	}

	public function getMonthName()
	{
		return (integer)$this->format('F');
	}

	public function addSeconds($seconds)
	{
		$seconds = (float)$seconds;
		$interval = new HotDateInterval('PT' . $seconds . 'S');
		return $this->add($interval);
	}

	public function subtractSeconds($seconds)
	{
		$seconds = (float)$seconds;
		$seconds = -$seconds;
		return $this->addSeconds($seconds);
	}

	public static function compare(HotDateTime $date1, HotDateTime $date2)
	{
		$seconds1 = $date1->getTimestamp();
		$seconds2 = $date2->getTimestamp();

		if ($seconds1 > $seconds2) {
			return 1;
		}

		if ($seconds1 < $seconds2) {
			return -1;
		}

		return 0;
	}

	public function setYear($year)
	{
		return $this->setDate(
			$year,
			$this->getMonth(),
			$this->getDay()
		);
	}

	public function setMonth($month)
	{
		return $this->setDate(
			$this->getYear(),
			$month,
			$this->getDay()
		);
	}

	public function setDay($day)
	{
		return $this->setDate(
			$this->getYear(),
			$this->getMonth(),
			$day
		);
	}

	public function setHour($hour)
	{
		return $this->setTime(
			$hour,
			$this->getMinute(),
			$this->getSecond()
		);
	}

	public function setMinute($minute)
	{
		return $this->setTime(
			$this->getHour(),
			$minute,
			$this->getSecond()
		);
	}

	public function setSecond($second)
	{
		return $this->setTime(
			$this->getHour(),
			$this->getMinute(),
			$second
		);
	}

	public function before(HotDateTime $when)
	{
		return (self::compare($this, $when) == -1);
	}

	public function after(HotDateTime $when)
	{
		return (self::compare($this, $when) == 1);
	}

	public function equals(HotDateTime $when)
	{
		return (self::compare($this, $when) == 0);
	}
}

?>
