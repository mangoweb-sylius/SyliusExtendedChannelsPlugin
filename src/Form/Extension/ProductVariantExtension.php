<?php

declare(strict_types=1);

namespace MangoSylius\ExtendedChannelsPlugin\Form\Extension;

use MangoSylius\ExtendedChannelsPlugin\Form\EventSubscriber\AddCodeFormSubscriber;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

final class ProductVariantExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventSubscriber(new AddCodeFormSubscriber());
    }

    /** @return array<int, string> */
    public static function getExtendedTypes(): array
    {
        return [
            \Sylius\Bundle\ProductBundle\Form\Type\ProductVariantType::class,
        ];
    }
}
