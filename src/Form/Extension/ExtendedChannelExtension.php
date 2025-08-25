<?php

declare(strict_types=1);

namespace MangoSylius\ExtendedChannelsPlugin\Form\Extension;

use MangoSylius\ExtendedChannelsPlugin\Entity\TimezoneEntity;
use Sylius\Bundle\AdminBundle\Form\Type\ChannelType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;

final class ExtendedChannelExtension extends AbstractTypeExtension
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options,
    ): void {
        $builder
            ->add('bccEmail', EmailType::class, [
                'label' => 'mango-sylius.admin.form.channel.bccEmail',
                'required' => false,
                'constraints' => [
                    new Email([
                        'groups' => ['sylius'],
                    ]),
                ],
            ])
            ->add('contactPhone', TextType::class, [
                'label' => 'mango-sylius.admin.form.channel.contactPhone',
                'required' => false,
            ])
            ->add('timezone', EntityType::class, [
                'label' => 'mango-sylius.admin.form.channel.timezone',
                'required' => false,
                'class' => TimezoneEntity::class,
            ]);
    }

    /** @return array<int, string> */
    public static function getExtendedTypes(): array
    {
        return [
            ChannelType::class,
        ];
    }
}
