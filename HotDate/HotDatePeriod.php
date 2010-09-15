<?php

class HotDatePeriod implements Iterator
{
	const EXCLUDE_START_DATE = 1;

	public function __construct(
		$param1,
		$param2 = null,
		$param3 = null,
		$param4 = null
	) {
		if (is_string($param1)) {
			if (is_int($param2)) {
				$options = $param2;
			}
		} else {

			if (!($param1 instanceof HotDateTime)) {
				throw new InvalidArgumentException(
					'$start must be an instance of HotDateTime.'
				);
			}
			$start = $param1;

			if (!($param2 instanceof HotDateInterval)) {
				throw new InvalidArgumentException(
					'$interval must be an instance of HotDateInterval.'
				);
			}
			$interval = $param2;

			if (is_int($param3)) {
				$recurrences = $param3;
			} else {
				if (!($param3 instanceof HotDateTime)) {
					throw new InvalidArgumentException(
						'$end must be an instance of HotDateTime.'
					);
				}
				$end = $param3;
			}

			if (is_int($param4)) {
				$options = $param4;
			}
		}
	}

	public function current()
	{
	}

	public function key()
	{
	}

	public function next()
	{
	}

	public function rewind()
	{
	}

	public function valid()
	{
	}
}

?>
