<?php

namespace MangoSylius\ExtendedChannelsPlugin\Form\Type;

use Sylius\Bundle\CoreBundle\Form\Type\Taxon\ProductTaxonAutocompleteChoiceType;
use Sylius\Bundle\TaxonomyBundle\Form\Type\TaxonAutocompleteChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @method iterable getExtendedTypes()
 */
final class BulkManageProductCategoriesType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(
		FormBuilderInterface $builder,
		array $options
	): void {
		$builder
			->add('mainTaxon', TaxonAutocompleteChoiceType::class, [
				'label' => 'sylius.form.product.main_taxon',
			])
			->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
				$product = $event->getData();
				$form = $event->getForm();

				$form->add('productTaxons', ProductTaxonAutocompleteChoiceType::class, [
					'label' => 'sylius.form.product.taxons',
					'product' => $product,
					'multiple' => true,
				]);
			})
			->add('submit', SubmitType::class, [
				'label' => 'Submit',
			]);
	}
}
