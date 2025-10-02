<?php

declare(strict_types=1);

namespace MangoSylius\ExtendedChannelsPlugin\Form\EventSubscriber;

use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class AddCodeFormSubscriber implements EventSubscriberInterface
{
    private readonly string $type;

    /**
     * @param array<string, mixed> $options
     */
    public function __construct(
        ?string $type = null,
        private readonly array $options = [],
    ) {
        $this->type = $type ?? TextType::class;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
        ];
    }

    public function preSetData(FormEvent $event): void
    {
        $resource = $event->getData();

        if (!($resource instanceof CodeAwareInterface) && null !== $resource) {
            throw new UnexpectedTypeException($resource, CodeAwareInterface::class);
        }

        $form = $event->getForm();
        $form->add('code', $this->type, array_merge(
            ['label' => 'sylius.ui.code'],
            $this->options,
        ));
    }
}
