<?php

declare(strict_types=1);

namespace MangoSylius\ExtendedChannelsPlugin\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

final class TaxonTypeExtension extends AbstractTypeExtension
{
	/** @param array<mixed> $options */
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder->add(
			'externalLink',
			CheckboxType::class,
			[
				'required' => false,
				'label' => 'mango-sylius.admin.form.taxon.externalLink',
			]
		);
	}

	/** @return array<int, string> */
	public static function getExtendedTypes(): array
	{
		return [
			\Sylius\Bundle\TaxonomyBundle\Form\Type\TaxonType::class,
		];
	}
}
