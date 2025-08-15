<?php

declare(strict_types=1);

namespace Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Customer\Model\CustomerInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class OrderContext implements Context
{
    public function __construct(
        private readonly EntityManagerInterface             $entityManager,
        private readonly SharedStorageInterface             $sharedStorage,
        private readonly FactoryInterface                   $customerFactory,
        private readonly ProductVariantResolverInterface    $variantResolver,
        private readonly FactoryInterface                   $orderItemFactory,
        private readonly OrderItemQuantityModifierInterface $itemQuantityModifier,
        private readonly FactoryInterface                   $orderFactory,
        private readonly StateMachineFactoryInterface       $stateMachineFactory,
    ) {
    }

    /**
     * @Given /^the guest customer placed order with number "([^"]+)" with ("[^"]+" product) for "([^"]+)" and ("[^"]+" based shipping address) with ("[^"]+" shipping method) and ("[^"]+" payment)$/
     */
    public function theGuestCustomerPlacedOrderWithNumberWithProductForAndBasedShippingAddressWithShippingMethodAndPayment(
        string                  $number,
        ProductInterface        $product,
        string                  $email,
        AddressInterface        $address,
        ShippingMethodInterface $shippingMethod,
        PaymentMethodInterface  $paymentMethod,
    ) {
        /** @var CustomerInterface $customer */
        $customer = $this->customerFactory->createNew();
        $customer->setEmail($email);
        $customer->setFirstName('John');
        $customer->setLastName('Doe');

        $this->entityManager->persist($customer);

        $this->placeOrder($product, $shippingMethod, $address, $paymentMethod, $customer, $number);
        $this->entityManager->flush();
    }

    /**
     * @Given /^(this order) is "([^"]+)" days old$/
     */
    public function thisOrderIsDaysOld(
        OrderInterface $order,
        int            $days,
    ) {
        $date = new \DateTime();
        $date = $date->modify('-' . $days . ' day');
        $order->setCheckoutCompletedAt($date);

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        $this->sharedStorage->set('order', $order);
    }

    private function placeOrder(
        ProductInterface        $product,
        ShippingMethodInterface $shippingMethod,
        AddressInterface        $address,
        PaymentMethodInterface  $paymentMethod,
        CustomerInterface       $customer,
        string                  $number,
    ): void {
        /** @var ProductVariantInterface $variant */
        $variant = $this->variantResolver->getVariant($product);

        /** @var ChannelPricingInterface $channelPricing */
        $channelPricing = $variant->getChannelPricingForChannel($this->sharedStorage->get('channel'));
        assert($channelPricing !== null);
        $price = $channelPricing->getPrice();
        assert($price !== null);

        /** @var OrderItemInterface $item */
        $item = $this->orderItemFactory->createNew();
        $item->setVariant($variant);
        $item->setUnitPrice($price);

        $this->itemQuantityModifier->modify($item, 1);

        $order = $this->createOrder($customer, $number);
        $order->addItem($item);

        $this->checkoutUsing($order, $shippingMethod, clone $address, $paymentMethod);

        $this->entityManager->persist($order);
        $this->sharedStorage->set('order', $order);
    }

    /**
     * @param string $number
     * @param string|null $localeCode
     *
     * @return OrderInterface
     */
    private function createOrder(
        CustomerInterface $customer,
                          $number = null,
        ChannelInterface  $channel = null,
                          $localeCode = null,
    ) {
        $order = $this->createCart($customer, $channel, $localeCode);

        if (null !== $number) {
            $order->setNumber($number);
        }

        $order->completeCheckout();

        return $order;
    }

    /**
     * @param string|null $localeCode
     *
     * @return OrderInterface
     */
    private function createCart(
        CustomerInterface $customer,
        ChannelInterface  $channel = null,
                          $localeCode = null,
    ) {
        /** @var OrderInterface $order */
        $order = $this->orderFactory->createNew();

        $order->setCustomer($customer);
        $order->setChannel($channel ?? $this->sharedStorage->get('channel'));
        $order->setLocaleCode($localeCode ?? $this->sharedStorage->get('locale')->getCode());

        $channel = $order->getChannel();
        assert($channel !== null);
        $baseCurrency = $channel->getBaseCurrency();
        assert($baseCurrency !== null);
        $baseCurrencyCode = $baseCurrency->getCode();
        assert($baseCurrencyCode !== null);
        $order->setCurrencyCode($baseCurrencyCode);

        return $order;
    }

    private function checkoutUsing(
        OrderInterface          $order,
        ShippingMethodInterface $shippingMethod,
        AddressInterface        $address,
        PaymentMethodInterface  $paymentMethod,
    ) {
        $order->setShippingAddress($address);
        $order->setBillingAddress(clone $address);

        $this->applyTransitionOnOrderCheckout($order, OrderCheckoutTransitions::TRANSITION_ADDRESS);

        $this->proceedSelectingShippingAndPaymentMethod($order, $shippingMethod, $paymentMethod);
    }

    private function proceedSelectingShippingAndPaymentMethod(
        OrderInterface          $order,
        ShippingMethodInterface $shippingMethod,
        PaymentMethodInterface  $paymentMethod,
    ) {
        foreach ($order->getShipments() as $shipment) {
            $shipment->setMethod($shippingMethod);
        }
        $this->applyTransitionOnOrderCheckout($order, OrderCheckoutTransitions::TRANSITION_SELECT_SHIPPING);

        $payment = $order->getLastPayment(PaymentInterface::STATE_CART);
        assert($payment !== null);
        $payment->setMethod($paymentMethod);

        $this->applyTransitionOnOrderCheckout($order, OrderCheckoutTransitions::TRANSITION_SELECT_PAYMENT);
        $this->applyTransitionOnOrderCheckout($order, OrderCheckoutTransitions::TRANSITION_COMPLETE);
    }

    /**
     * @param string $transition
     */
    private function applyTransitionOnOrderCheckout(
        OrderInterface $order,
                       $transition,
    ) {
        $this->stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->apply($transition);
    }
}
