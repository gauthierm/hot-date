<?php

class HotDateInterval
{
	public $y = 0;
	public $m = 0;
	public $d = 0;
	public $h = 0;
	public $i = 0;
	public $s = 0;
	public $invert = false;
	public $days = false;

	public function __construct($intervalSpec)
	{
		$result = self::parseIntervalSpec($intervalSpec);
		foreach ($result as $key => $value) {
			if (property_exists($this, strtolower($key))) {
				$this->{strtolower($key)} = $value;
			}
		}
	}

	public static function createFromDateString($time)
	{
		$now    = new HotDateTime();
		$offset = new HotDateTime($time);
		return $date->diff($offset, $now);
	}

	public function format($format)
	{
		$output = '';

		$len = strlen($format);
		for ($i = 0; $i < $len; $i += 1) {
			if ($format[$i] === '%' && $i < $len - 1) {
				$code = substr($format, $i, 2);
				switch ($code) {
				case '%%':
					$output .= '%';
					break;
				case '%Y':
					$output .= str_pad($this->y, 2, '0', STR_PAD_LEFT);
					break;
				case '%y':
					$output .= $this->y;
					break;
				case '%M':
					$output .= str_pad($this->m, 2, '0', STR_PAD_LEFT);
					break;
				case '%m':
					$output .= $this->m;
					break;
				case '%D':
					$output .= str_pad($this->d, 2, '0', STR_PAD_LEFT);
					break;
				case '%d':
					$output .= $this->d;
					break;
				case '%a':
					$output .= $this->days;
					break;
				case '%H':
					$output .= str_pad($this->h, 2, '0', STR_PAD_LEFT);
					break;
				case '%h':
					$output .= $this->h;
					break;
				case '%I':
					$output .= str_pad($this->i, 2, '0', STR_PAD_LEFT);
					break;
				case '%i':
					$output .= $this->i;
					break;
				case '%S':
					$output .= str_pad($this->s, 2, '0', STR_PAD_LEFT);
					break;
				case '%s':
					$output .= $this->s;
					break;
				case '%R':
					if ($this->invert) {
						$output .= '-';
					} else {
						$output .= '+';
					}
					break;
				case '%r':
					if ($this->invert) {
						$output .= '-';
					}
					break;
				}
				$i++;
			} else {
				$output .= $format[$i];
			}
		}
	}

	protected static function parseIntervalSpec($spec)
	{
		$result = array();

		if (preg_match('/[YMDWHS]/', $spec) === 1) {
			$hasTime = false;
			$num = '';
			$len = strlen($spec);
			for ($i = 0; $i < $len; $i++) {
				switch ($spec[$i]) {
				case 'P':
					break;
				case 'T':
					$hasTime = true;
					break;
				case '1':
				case '2':
				case '3':
				case '4':
				case '5':
				case '6':
				case '7':
				case '8':
				case '9':
				case '0':
				case '-':
				case '.':
					$num .= $spec[$i];
					break;
				case 'Y':
				case 'M':
				case 'D':
				case 'H':
				case 'S':
					if ($hasTime && $spec[$i] === 'M') {
						$result['I'] = (float)$num;
					} else {
						$result[$spec[$i]] = (float)$num;
					}
					$num = '';
					break;
				case 'W':
					$result['D'] = (float)$num * 7;
					$num = '';
					break;
				}
			}
		} else {
			$spec = str_replace(array('P', 'T', ':', '-'), '', $spec);
			$result['Y'] = (integer)substr($spec, 0, 4);
			$result['M'] = (integer)substr($spec, 4, 2);
			$result['D'] = (integer)substr($spec, 6, 2);
			$result['H'] = (integer)substr($spec, 8, 2);
			$result['I'] = (integer)substr($spec, 10, 2);
			$result['S'] = (integer)substr($spec, 12);
		}

		return $result;
	}
}

?>
