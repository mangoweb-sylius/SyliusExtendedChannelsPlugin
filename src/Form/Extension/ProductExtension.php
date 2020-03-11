<?php

declare(strict_types=1);

namespace MangoSylius\ExtendedChannelsPlugin\Form\Extension;

use MangoSylius\ExtendedChannelsPlugin\Form\EventSubscriber\AddCodeFormSubscriber;
use Sylius\Bundle\ProductBundle\Form\Type\ProductType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @method iterable getExtendedTypes()
 */
final class ProductExtension extends AbstractTypeExtension
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder->addEventSubscriber(new AddCodeFormSubscriber());
	}

	/**
	 * {@inheritdoc}
	 */
	public function getExtendedType(): string
	{
		return ProductType::class;
	}
}
