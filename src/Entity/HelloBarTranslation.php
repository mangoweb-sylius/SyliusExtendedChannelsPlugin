<?php

declare(strict_types=1);

namespace MangoSylius\ExtendedChannelsPlugin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\AbstractTranslation;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="mangoweb_bello_bar_translation")
 */
class HelloBarTranslation extends AbstractTranslation implements ResourceInterface, HelloBarTranslationInterface
{
	/**
	 * @var int|null
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @var string|null
	 * @ORM\Column(type="text", nullable=true)
	 */
	protected $title;

	/**
	 * @var string|null
	 * @ORM\Column(type="text", nullable=true)
	 */
	protected $content;

	public function getId(): ?int
	{
		return $this->id;
	}

	private function getFallbackTranslation(): HelloBarTranslationInterface
	{
		assert($this->translatable instanceof HelloBarInterface);
		$trans = $this->translatable->getTranslation($this->translatable->getFallbackLocale());
		assert($trans instanceof HelloBarTranslationInterface);

		return $trans;
	}

	public function getTitle(): ?string
	{
		return $this->title;
	}

	public function setTitle(?string $title): void
	{
		$this->title = $title;
	}

	public function getContent(): ?string
	{
		return $this->content;
	}

	public function setContent(?string $content): void
	{
		$this->content = $content;
	}
}
