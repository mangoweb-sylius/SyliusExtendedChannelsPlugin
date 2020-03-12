<?php

declare(strict_types=1);

namespace MangoSylius\ExtendedChannelsPlugin\Menu;

use Knp\Menu\ItemInterface;
use Sylius\Bundle\AdminBundle\Event\OrderShowMenuBuilderEvent;
use Sylius\Bundle\AdminBundle\Event\ProductMenuBuilderEvent;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

class AdminMenuListener
{
	public function addAdminMenuItems(MenuBuilderEvent $event): void
	{
		$menu = $event->getMenu();
		assert($menu->getChild('configuration') instanceof ItemInterface);

		$menu->getChild('configuration')
			->addChild('hello_bar', ['route' => 'mangoweb_extended_channels_plugin_admin_hello_bar_index'])
			->setLabel('mango-sylius.admin.hello_bar.menu')
			->setLabelAttribute('icon', 'industry')
		;
	}

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
