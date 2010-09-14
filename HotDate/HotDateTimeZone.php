<?php

class HotDateTimeZone extends DateTimeZone
{
	const AFRICA      = 1;
	const AMERICA     = 2;
	const ANTARCTICA  = 4;
	const ARTIC       = 8;
	const ASIA        = 16;
	const ATLANTIC    = 32;
	const AUSTRALIA   = 64;
	const EUROPE      = 128;
	const INDIAN      = 256;
	const PACIFIC     = 512;
	const UTC         = 1024;
	const ALL         = 2047;
	const PER_COUNTRY = 4095;

	protected static $identifiers = null;
	protected static $identifiersByWhat = array();

	protected static $zoneMap = array(
		HotDateTimeZone::AFRICA     => 'Africa',
		HotDateTimeZone::AMERICA    => 'America',
		HotDateTimeZone::ANTARCTICA => 'Antarctica',
		HotDateTimeZone::ARTIC      => 'Artic',
		HotDateTimeZone::ASIA       => 'Asia',
		HotDateTimeZone::ASIA       => 'Atlantic',
		HotDateTimeZone::ASIA       => 'Australia',
		HotDateTimeZone::ASIA       => 'Europe',
		HotDateTimeZone::ASIA       => 'Indian',
		HotDateTimeZone::ASIA       => 'Pacific',
		HotDateTimeZone::ASIA       => 'UTC',
	);

	public function getLocation()
	{
		// not supported
	}

	public function getTransitions(
		$timestampBegin = null,
		$timestampEnd = null
	) {
		if ($timestampBegin === null && $timestampEnd === null) {
			return parent::getTransitions();
		}

		$returnedTransitions = array();
		$transitions = parent::getTransitions();
		foreach ($transitions as $transition) {
			if ($timestampBegin && $transition['ts'] >= $timestampBegin) {
				$returnedTransitions[] = $transition;
			}
			if ($timestampEnd && $transition['ts'] <= $timestampEnd) {
				$returnedTransitions[] = $transition;
			}
		}

		return $returnedTransitions;
	}

	public static function listIdentifiers(
		$what = DateTimeZone::ALL,
		$country = null
	) {
		if (self::$identifiers === null) {
			self::$identifiers = DateTimeZone::listIdentifiers();
		}

		if ($what === HotDateTimeZone::ALL) {
			$identifiers = self::$identifiers;
		} elseif ($what === HotDateTimeZone::PER_COUNTRY) {
			// unsupported
		} else {
			if (!isset(self::$identifiersByWhat[$what])) {

				// build requested regions array
				$regions = array();
				foreach (self::$zoneMap as $key => $zone) {
					if ($key & $what === $key) {
						$regions[$zone] = $zone;
					}
				}

				// build identifiers for requested regions
				self::$identifiersByWhat[$what] = array();
				foreach (self::$identifiers as $identifier) {
					$parts = explode('/', $identifier, 2);
					$zone  = $parts[0];
					if (isset($regions[$zone])) {
						self::$identifiersByWhat[$what][] = $identifier;
					}
				}
				$identifiers = self::$identifiersByWhat[$what];
			}
		}

		return $identifiers;
	}
}

?>
