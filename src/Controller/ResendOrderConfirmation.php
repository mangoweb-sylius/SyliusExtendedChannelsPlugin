<?php

declare(strict_types=1);

namespace MangoSylius\ExtendedChannelsPlugin\Controller;

use Liip\ImagineBundle\Exception\Config\Filter\NotFoundException;
use Sylius\Bundle\CoreBundle\Mailer\Emails;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

class ResendOrderConfirmation
{
	/**
	 * @var RouterInterface
	 */
	private $router;
	/**
	 * @var FlashBagInterface
	 */
	private $flashBag;
	/**
	 * @var TranslatorInterface
	 */
	private $translator;
	/**
	 * @var SenderInterface
	 */
	private $emailSender;
	/**
	 * @var OrderRepositoryInterface
	 */
	private $orderRepository;

	public function __construct(
		TranslatorInterface $translator,
		FlashBagInterface $flashBag,
		RouterInterface $router,
		SenderInterface $emailSender,
		OrderRepositoryInterface $orderRepository
	) {
		$this->router = $router;
		$this->flashBag = $flashBag;
		$this->translator = $translator;
		$this->emailSender = $emailSender;
		$this->orderRepository = $orderRepository;
	}

	public function __invoke(int $id): RedirectResponse
	{
		$order = $this->orderRepository->find($id);
		if ($order === null) {
			throw new NotFoundException();
		}

		assert($order instanceof OrderInterface);
		if ($order->getCustomer() !== null) {
			$this->emailSender->send(
				Emails::ORDER_CONFIRMATION,
				[$order->getCustomer()->getEmail()],
				['order' => $order]
			);
		}

		$message = $this->translator->trans('mango-sylius.admin.order.successEmailResend');
		$this->flashBag->add('success', $message);

		return new RedirectResponse($this->router->generate('sylius_admin_order_show', ['id' => $id]));
	}
}
