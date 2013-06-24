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
			$this->dateTime->getTimestamp(),
			$this->timeZone->getName(),
		);

		return serialize($data);
	}

	public function unserialize($serialized)
	{
		$data = unserialize($serialized);

		$this->timeZone = new DateTimeZone($data[1]);
		$this->dateTime = new DateTime('@' . $data[0]);

		// DateTime constructor with timestamp is always UTC
		$this->dateTime->setTimezone($this->timeZone);
	}
}

?>
