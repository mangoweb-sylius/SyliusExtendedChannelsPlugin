<?php

declare(strict_types=1);

namespace MangoSylius\ExtendedChannelsPlugin\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Model\TranslatableTrait;
use Sylius\Component\Resource\Model\TranslationInterface;

/**
 * @ORM\Entity
 *
 * @ORM\Table(name="mangoweb_hello_bar")
 */
#[ORM\Entity]
#[ORM\Table(name: 'mangoweb_hello_bar')]
class HelloBar implements ResourceInterface, TranslatableInterface, HelloBarInterface
{
    use TranslatableTrait {
        __construct as private initializeTranslationsCollection;
        getTranslation as private doGetTranslation;
    }

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

    /** @ORM\Column(type="datetime", nullable=true) */
    #[ORM\Column(type: 'datetime', nullable: true)]
    protected ?\DateTime $startsAt = null;

    /** @ORM\Column(type="datetime", nullable=true) */
    #[ORM\Column(type: 'datetime', nullable: true)]
    protected ?\DateTime $endsAt = null;

    /**
     * @var Collection|ChannelInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Sylius\Component\Core\Model\Channel")
     *
     * @ORM\JoinTable(name="mangoweb_hello_bar_channel",
     *     joinColumns={@ORM\JoinColumn(name="mangoweb_hello_bar_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="channel_id", referencedColumnName="id")}
     * )
     */
    #[ORM\ManyToMany(targetEntity: ChannelInterface::class)]
    #[ORM\JoinTable(name: 'mangoweb_hello_bar_channel')]
    #[ORM\JoinColumn(name: 'mangoweb_hello_bar_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'channel_id', referencedColumnName: 'id')]
    protected Collection $channels;

    /** @ORM\Column(type="string", nullable=false) */
    #[ORM\Column(type: 'string', nullable: false)]
    protected ?string $messageType = null;

    public function __construct()
    {
        $this->channels = new ArrayCollection();
        $this->initializeTranslationsCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFallbackLocale(): ?string
    {
        return $this->fallbackLocale;
    }

    public function setTitle(?string $name): void
    {
        $this->getTranslation()->setTitle($name);
    }

    public function getTitle(): ?string
    {
        return $this->getTranslation()->getTitle();
    }

    public function setContent(?string $name): void
    {
        $this->getTranslation()->setContent($name);
    }

    public function getContent(): ?string
    {
        return $this->getTranslation()->getContent();
    }

    /**
     * @return HelloBarTranslationInterface
     */
    public function getTranslation(?string $locale = null): TranslationInterface
    {
        /** @var HelloBarTranslationInterface $translation */
        $translation = $this->doGetTranslation($locale);

        return $translation;
    }

    protected function createTranslation(): HelloBarTranslationInterface
    {
        return new HelloBarTranslation();
    }

    /**
     * @return Collection<ChannelInterface>
     */
    public function getChannels(): Collection
    {
        return $this->channels;
    }

    /**
     * @param Collection<ChannelInterface> $channels
     */
    public function setChannels($channels): void
    {
        $this->channels = $channels;
    }

    public function getMessageType(): ?string
    {
        return $this->messageType;
    }

    public function setMessageType(?string $messageType): void
    {
        $this->messageType = $messageType;
    }

    public function getStartsAt(): ?\DateTime
    {
        return $this->startsAt;
    }

    public function setStartsAt(?\DateTime $startsAt): void
    {
        $this->startsAt = $startsAt;
    }

    public function getEndsAt(): ?\DateTime
    {
        return $this->endsAt;
    }

    public function setEndsAt(?\DateTime $endsAt): void
    {
        $this->endsAt = $endsAt;
    }
}
