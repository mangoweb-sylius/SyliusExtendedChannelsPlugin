<?php

declare(strict_types=1);

namespace MangoSylius\ExtendedChannelsPlugin\Controller;

use Doctrine\ORM\EntityManagerInterface;
use MangoSylius\ExtendedChannelsPlugin\Form\Type\BulkManageProductCategoriesType;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

class ManageProductCategoriesController
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
	 * @var ProductRepositoryInterface
	 */
	private $productRepository;

	/** @var EngineInterface */
	private $templatingEngine;

	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;

	/**
	 * @var FormFactoryInterface
	 */
	private $formFactory;

	public function __construct(
		TranslatorInterface $translator,
		FlashBagInterface $flashBag,
		RouterInterface $router,
		ProductRepositoryInterface $productRepository,
		EngineInterface $templatingEngine,
		EntityManagerInterface $entityManager,
		FormFactoryInterface $formFactory
	) {
		$this->router = $router;
		$this->flashBag = $flashBag;
		$this->translator = $translator;
		$this->productRepository = $productRepository;
		$this->templatingEngine = $templatingEngine;
		$this->entityManager = $entityManager;
		$this->formFactory = $formFactory;
	}

	public function bulkManageProductCategories(Request $request): Response
	{
		$productIds = array_filter(explode(',', $request->get('bulkProductsIds')));
		assert($productIds);

		if ($request->isMethod('POST')) {
			foreach ($productIds as $productId) {
				$product = $this->productRepository->find($productId);
				assert($product instanceof ProductInterface);
				$product->getProductTaxons()->clear();
				$form = $this->formFactory->create(BulkManageProductCategoriesType::class, $product);
				$form->handleRequest($request);
			}
			$this->entityManager->flush();

			$message = $this->translator->trans('mango-sylius.admin.manage_product_categories.saved');
			$this->flashBag->add('success', $message);

			return new RedirectResponse($this->router->generate('sylius_admin_product_index'));
		}

		$mainProduct = $this->productRepository->find(current($productIds));
		assert($mainProduct instanceof ProductInterface);
		$mainProduct->getProductTaxons()->clear();
		$form = $this->formFactory->create(BulkManageProductCategoriesType::class, $mainProduct);

		return $this->templatingEngine->renderResponse('@MangoSyliusExtendedChannelsPlugin/ManageProductCategories/form.html.twig', [
			'form' => $form->createView(),
			'productIds' => implode(',', $productIds),
			'paths' => [
				'cancel' => $this->router->generate('sylius_admin_product_index'),
			],
		]);
	}
}
