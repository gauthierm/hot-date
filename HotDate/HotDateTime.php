<?php

/**
 * Transitional adapter for DateTime that supports unserializing from
 * legacy HotDate
 */
class HotDateTime extends DateTime implements Serializable
{
	public function serialize()
	{
		$data = array(
			$this->getTimestamp(),
			$this->getTimeZone()->getName(),
		);

		return serialize($data);
	}

	public function unserialize($serialized)
	{
		$data = unserialize($serialized);

		$this->__construct('@'.$data[0]);

		// DateTime constructor with timestamp is always UTC
		$this->setTimezone(new DateTimeZone($data[1]));
	}
}

?>
