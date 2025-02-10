<?php

declare(strict_types=1);

namespace MangoSylius\ExtendedChannelsPlugin\Service;

use Psr\Log\LoggerInterface;
use Sylius\Component\Mailer\Event\EmailSendEvent;
use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Renderer\RenderedEmail;
use Sylius\Component\Mailer\Sender\Adapter\AbstractAdapter;
use Sylius\Component\Mailer\SyliusMailerEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SwiftMailerAdapter extends AbstractAdapter
{
    /** @var \Swift_Mailer */
    protected $mailer;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        \Swift_Mailer $mailer,
        LoggerInterface $logger,
        EventDispatcherInterface $dispatcher,
    ) {
        $this->mailer = $mailer;
        $this->logger = $logger;
        $this->setEventDispatcher($dispatcher);
    }

    /**
     * @phpstan-ignore-next-line missingType.iterableValue
     */
    public function send(
        array $recipients,
        string $senderAddress,
        string $senderName,
        RenderedEmail $renderedEmail,
        EmailInterface $email,
        array $data,
        array $attachments = [],
        array $replyTo = [],
    ): void {
        $message = (new \Swift_Message())
            ->setSubject($renderedEmail->getSubject())
            ->setFrom([$senderAddress => $senderName])
            ->setTo($recipients)
            ->setReplyTo($replyTo);

        $message->setBody($renderedEmail->getBody(), 'text/html');

        foreach ($attachments as $attachment) {
            $file = \Swift_Attachment::fromPath($attachment);

            $message->attach($file);
        }

        $emailSendEvent = new EmailSendEvent($message, $email, $data, $recipients, $replyTo);

        assert($this->dispatcher instanceof EventDispatcherInterface);

        $this->dispatcher->dispatch($emailSendEvent, SyliusMailerEvents::EMAIL_PRE_SEND);

        try {
            $this->mailer->send($message);
        } catch (\Swift_TransportException $err) {
            $this->logger->warning($err->getMessage());
        } catch (\Swift_RfcComplianceException $err) {
            $this->logger->error($err->getMessage());
        } catch (\Swift_SwiftException $err) {
            $this->logger->error($err->getMessage());
        } catch (\Throwable $err) {
            $this->logger->error($err->getMessage());
        }

        $this->dispatcher->dispatch($emailSendEvent, SyliusMailerEvents::EMAIL_POST_SEND);
    }
}
