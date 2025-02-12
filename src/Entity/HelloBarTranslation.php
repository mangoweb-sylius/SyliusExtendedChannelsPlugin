<?php

declare(strict_types=1);

namespace MangoSylius\ExtendedChannelsPlugin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\AbstractTranslation;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @ORM\Entity
 *
 * @ORM\Table(name="mangoweb_hello_bar_translation")
 */
#[ORM\Entity]
#[ORM\Table(name: 'mangoweb_hello_bar_translation')]
class HelloBarTranslation extends AbstractTranslation implements ResourceInterface, HelloBarTranslationInterface
{
    /**
     * @ORM\Id
     *
     * @ORM\Column(type="integer")
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    /** @ORM\Column(type="text", nullable=true) */
    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $title = null;

    /** @ORM\Column(type="text", nullable=true) */
    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $content = null;

    public function getId(): ?int
    {
        return $this->id;
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
