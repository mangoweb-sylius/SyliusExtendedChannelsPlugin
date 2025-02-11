<?php

declare(strict_types=1);

namespace MangoSylius\ExtendedChannelsPlugin\Service;

use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;
use Psr\Log\LoggerInterface;
use Sylius\Component\Mailer\Event\EmailSendEvent;
use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Renderer\RenderedEmail;
use Sylius\Component\Mailer\Sender\Adapter\AbstractAdapter;
use Sylius\Component\Mailer\SyliusMailerEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class MailerAdapter extends AbstractAdapter
{
    public function __construct(
        protected MailerInterface $mailer,
        private LoggerInterface $logger,
        EventDispatcherInterface $dispatcher,
    ) {
        $this->setEventDispatcher($dispatcher);
    }

    /**
     * @inheritdoc
     *
     * @param array<int|string, string> $recipients
     *
     * @phpstan-ignore missingType.iterableValue
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
        $message = (new Email())
            ->subject($renderedEmail->getSubject())
            ->from(new Address($senderAddress, $senderName))
            ->to(...$this->formatRecipients($recipients))
            ->replyTo(...$this->formatRecipients($replyTo))
            ->html($renderedEmail->getBody());

        foreach ($attachments as $attachment) {
            $message->attachFromPath($attachment);
        }

        $emailSendEvent = new EmailSendEvent($message, $email, $data, $recipients, $replyTo);

        assert($this->dispatcher instanceof EventDispatcherInterface);

        $this->dispatcher->dispatch($emailSendEvent, SyliusMailerEvents::EMAIL_PRE_SEND);

        try {
            $this->mailer->send($message);
        } catch (TransportExceptionInterface $transportException) {
            $this->logger->warning($transportException->getMessage());
        } catch (\Throwable $throwable) {
            $this->logger->error($throwable->getMessage());
        }

        $this->dispatcher->dispatch($emailSendEvent, SyliusMailerEvents::EMAIL_POST_SEND);
    }

    /**
     * @param array<int|string, string> $recipients
     *
     * @return array<Address|string>
     */
    protected function formatRecipients(array $recipients): array
    {
        $transformedRecipients = [];
        $validator = new EmailValidator();
        foreach ($recipients as $addressOrKey => $nameOrAddress) {
            if (\is_string($addressOrKey) && $validator->isValid($addressOrKey, new RFCValidation())) {
                $transformedRecipients[] = new Address($addressOrKey, $nameOrAddress);

                continue;
            }

            $transformedRecipients[] = $nameOrAddress;
        }

        return $transformedRecipients;
    }
}

class_alias(MailerAdapter::class, 'MangoSylius\ExtendedChannelsPlugin\Service\SwiftMailerAdapter', false);
