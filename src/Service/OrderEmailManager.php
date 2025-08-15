<?php

declare(strict_types=1);

namespace MangoSylius\ExtendedChannelsPlugin\Service;

use MangoSylius\ExtendedChannelsPlugin\Model\ExtendedChannelInterface;
use Sylius\Bundle\CoreBundle\Mailer\Emails;
use Sylius\Bundle\ShopBundle\EmailManager\OrderEmailManagerInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;

final class OrderEmailManager implements OrderEmailManagerInterface
{
    public function __construct(
        private readonly SenderInterface $emailSender,
        private readonly ChannelContextInterface $channelContext,
    ) {
    }

    public function sendConfirmationEmail(OrderInterface $order): void
    {
        $channel = $this->channelContext->getChannel();
        \assert($channel instanceof ExtendedChannelInterface);

        if ($channel->getBccEmail() !== null) {
            $this->emailSender->send(
                Emails::ORDER_CONFIRMATION,
                [$channel->getBccEmail()],
                [
                    'order' => $order,
                    'channel' => $order->getChannel(),
                    'localeCode' => $order->getLocaleCode(),
                ],
            );
        }

        if ($order->getCustomer() !== null) {
            $this->emailSender->send(
                Emails::ORDER_CONFIRMATION,
                [$order->getCustomer()->getEmail()],
                [
                    'order' => $order,
                    'channel' => $order->getChannel(),
                    'localeCode' => $order->getLocaleCode(),
                ],
            );
        }
    }
}
