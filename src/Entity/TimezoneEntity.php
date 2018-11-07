<?php

declare(strict_types=1);

namespace MangoSylius\ExtendedChannelsPlugin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="mango_timezone")
 * @ORM\Entity
 */
class TimezoneEntity
{
	/**
	 * @var int
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @var string
	 * @ORM\Column(type="string", name="timezone_title")
	 */
	private $timezoneTitle;

	public function getId(): int
	{
		return $this->id;
	}

	public function getTimezoneTitle(): string
	{
		return $this->timezoneTitle;
	}

	public function __toString(): string
	{
		return $this->timezoneTitle;
	}
}
