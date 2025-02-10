<?php

declare(strict_types=1);

namespace MangoSylius\ExtendedChannelsPlugin\Service;

use Doctrine\ORM\EntityRepository;
use SM\Factory\Factory;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\OrderShippingStates;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Updater\UnpaidOrdersStateUpdaterInterface;
use Sylius\Component\Order\OrderTransitions;
use Sylius\Component\Payment\Model\PaymentInterface;

class MangoUnpaidOrdersStateUpdater implements UnpaidOrdersStateUpdaterInterface
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var Factory */
    private $stateMachineFactory;

    /** @var string */
    private $expirationPeriod;

    /** @var string[] */
    private $expirationMethodCodes;

    /**
     * @param array<string> $expirationMethodCodes
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        Factory $stateMachineFactory,
        string $expirationPeriod,
        array $expirationMethodCodes,
    ) {
        $this->orderRepository = $orderRepository;
        $this->stateMachineFactory = $stateMachineFactory;
        $this->expirationPeriod = $expirationPeriod;
        $this->expirationMethodCodes = $expirationMethodCodes;
    }

    public function cancel(): void
    {
        $date = new \DateTime('-' . $this->expirationPeriod);

        /** @var EntityRepository $repo */
        $repo = $this->orderRepository;
        $expiredUnpaidOrders = $repo->createQueryBuilder('o')
            ->join('o.payments', 'p')
            ->join('p.method', 'm')
            ->where('o.checkoutState = :checkoutState')
            ->andWhere('o.paymentState != :paymentState')
            ->andWhere('o.shippingState NOT IN (:shippingState)')
            ->andWhere('o.state = :orderState')
            ->andWhere('o.checkoutCompletedAt < :terminalDate')
            ->andWhere('m.code IN (:expirationMethodCodes)')
            ->setParameter('checkoutState', OrderCheckoutStates::STATE_COMPLETED)
            ->setParameter('paymentState', OrderPaymentStates::STATE_PAID)
            ->setParameter('shippingState', [OrderShippingStates::STATE_SHIPPED, OrderShippingStates::STATE_PARTIALLY_SHIPPED])
            ->setParameter('orderState', OrderInterface::STATE_NEW)
            ->setParameter('terminalDate', $date)
            ->setParameter('expirationMethodCodes', $this->expirationMethodCodes)
            ->getQuery()
            ->getResult();

        foreach ($expiredUnpaidOrders as $expiredUnpaidOrder) {
            assert($expiredUnpaidOrder instanceof OrderInterface);

            $payments = $expiredUnpaidOrder->getPayments()->toArray();
            usort($payments, function (
                PaymentInterface $a,
                PaymentInterface $b,
            ) {
                $timestamp1 = $a->getCreatedAt() === null
                    ? 0
                    : $a->getCreatedAt()->getTimestamp();
                $timestamp2 = $b->getCreatedAt() === null
                    ? 0
                    : $b->getCreatedAt()->getTimestamp();

                return $timestamp2 <=> $timestamp1;
            });

            $lastPayment = count($payments) > 0
                ? $payments[0]
                : null;
            if ($lastPayment !== null) {
                assert($lastPayment instanceof PaymentInterface);
                $paymentMethod = $lastPayment->getMethod();
                assert($paymentMethod instanceof PaymentMethodInterface);
                if (in_array($paymentMethod->getCode(), $this->expirationMethodCodes, true)) {
                    $this->cancelOrder($expiredUnpaidOrder);
                }
            }
        }
    }

    private function cancelOrder(OrderInterface $expiredUnpaidOrder): void
    {
        $stateMachine = $this->stateMachineFactory->get($expiredUnpaidOrder, OrderTransitions::GRAPH);
        $stateMachine->apply(OrderTransitions::TRANSITION_CANCEL);
    }
}
