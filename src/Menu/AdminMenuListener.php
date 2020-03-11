<?php

declare(strict_types=1);

namespace MangoSylius\ExtendedChannelsPlugin\Menu;

use Sylius\Bundle\AdminBundle\Event\OrderShowMenuBuilderEvent;
use Sylius\Bundle\AdminBundle\Event\ProductMenuBuilderEvent;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

class AdminMenuListener
{
	/**
	 * @param MenuBuilderEvent $event
	 */
	public function addButtonsToOrder(MenuBuilderEvent $event): void
	{
		$menu = $event->getMenu();

		assert($event instanceof OrderShowMenuBuilderEvent);
		$order = $event->getOrder();

		$menu
			->addChild('resend_email_confirmation', [
				'route' => 'mango_sylius_admin_resend_order_confirmation',
				'routeParameters' => ['id' => $order->getId()],
			])
			->setAttribute('confirmation', true)
			->setLabel('mango-sylius.admin.order.resend_email_confirmation')
			->setLabelAttribute('color', 'blue')
			->setLabelAttribute('icon', 'send')
		;
	}

	/**
	 * @param MenuBuilderEvent $event
	 */
	public function addButtonsToProduct(MenuBuilderEvent $event): void
	{
		$menu = $event->getMenu();

		assert($event instanceof ProductMenuBuilderEvent);
		$product = $event->getProduct();

		$menu
			->addChild('duplicate', [
				'route' => 'mango_sylius_admin_duplicate_product',
				'routeParameters' => ['id' => $product->getId()],
			])
			->setAttribute('confirmation', true)
			->setLabel('mango-sylius.admin.product.duplicate')
			->setLabelAttribute('color', 'blue')
			->setLabelAttribute('icon', 'copy')
		;
	}
}
