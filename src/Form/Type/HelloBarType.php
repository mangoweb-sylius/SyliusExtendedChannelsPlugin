<?php

declare(strict_types=1);

namespace MangoSylius\ExtendedChannelsPlugin\Form\Type;

use Sylius\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Valid;

final class HelloBarType extends AbstractResourceType
{
	/**
	 * @var array
	 */
	private $helloBarTypes;

	public function __construct(array $helloBarTypes, string $dataClass, array $validationGroups = [])
	{
		parent::__construct($dataClass, $validationGroups);
		$this->helloBarTypes = $helloBarTypes;
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add('translations', ResourceTranslationsType::class, [
				'entry_type' => HelloBarTranslationType::class,
				'validation_groups' => ['sylius'],
				'constraints' => [
					new Valid(['groups' => ['sylius']]),
				],
			])
			->add('channels', ChannelChoiceType::class, [
				'multiple' => true,
				'expanded' => true,
				'label' => 'sylius.form.product.channels',
			])
			->add('startsAt', DateTimeType::class, [
				'required' => false,
				'date_widget' => 'single_text',
				'time_widget' => 'single_text',
			])
			->add('endsAt', DateTimeType::class, [
				'required' => false,
				'date_widget' => 'single_text',
				'time_widget' => 'single_text',
			])
			->add('messageType', ChoiceType::class, [
				'multiple' => false,
				'expanded' => false,
				'required' => true,
				'choices' => array_flip($this->helloBarTypes),
			])
		;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix(): string
	{
		return 'hello_bar';
	}
}
