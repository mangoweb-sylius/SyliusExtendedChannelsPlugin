<?php

declare(strict_types=1);

namespace MangoSylius\ExtendedChannelsPlugin\Controller;

use Doctrine\ORM\EntityManagerInterface;
use MangoSylius\ExtendedChannelsPlugin\Form\Type\BulkManageProductCategoriesType;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Product\Factory\ProductFactoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Symfony\Contracts\Translation\TranslatorInterface;

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

	/** @var Environment */
	private $twig;

	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;

	/**
	 * @var FormFactoryInterface
	 */
	private $formFactory;

	/**
	 * @var EventDispatcherInterface
	 */
	private $eventDispatcher;
	/**
	 * @var ProductFactoryInterface
	 */
	private $productFactory;
	/**
	 * @var FactoryInterface
	 */
	private $productTaxonFactory;

	public function __construct(
		TranslatorInterface $translator,
		FlashBagInterface $flashBag,
		RouterInterface $router,
		ProductRepositoryInterface $productRepository,
		Environment $twig,
		EntityManagerInterface $entityManager,
		FormFactoryInterface $formFactory,
		EventDispatcherInterface $eventDispatcher,
		ProductFactoryInterface $productFactory,
		FactoryInterface $productTaxonFactory
	) {
		$this->router = $router;
		$this->flashBag = $flashBag;
		$this->translator = $translator;
		$this->productRepository = $productRepository;
		$this->twig = $twig;
		$this->entityManager = $entityManager;
		$this->formFactory = $formFactory;
		$this->eventDispatcher = $eventDispatcher;
		$this->productFactory = $productFactory;
		$this->productTaxonFactory = $productTaxonFactory;
	}

	public function bulkManageProductCategories(Request $request): Response
	{
		/** @var array<int> $productIds */
		$productIds = array_filter(explode(',', $request->get('bulkProductsIds', [])));
		assert(count($productIds) > 0);

		$dummyProduct = $this->productFactory->createNew();
		assert($dummyProduct instanceof ProductInterface);
		$form = $this->formFactory->create(BulkManageProductCategoriesType::class, $dummyProduct);
		$form->handleRequest($request);

		if ($form->isSubmitted()) {
			$this->manageProducts($request, $dummyProduct, $productIds);

			$message = $this->translator->trans('mango-sylius.admin.manage_product_categories.saved');
			$this->flashBag->add('success', $message);

			// Eg. for update products in elasticsearch
			$event = new GenericEvent($productIds);
			$this->eventDispatcher->dispatch('mango-sylius-extended-channels.products.after_bulk_categories', $event);

			return new RedirectResponse($this->router->generate('sylius_admin_product_index'));
		}

		return new Response($this->twig->render('@MangoSyliusExtendedChannelsPlugin/ManageProductCategories/form.html.twig', [
			'form' => $form->createView(),
			'productIds' => implode(',', $productIds),
			'productIdsCount' => count($productIds),
			'paths' => [
				'cancel' => $this->router->generate('sylius_admin_product_index'),
			],
		]));
	}

	/**
	 * @param array<int> $productIds
	 */
	protected function manageProducts(Request $request, ProductInterface $dummyProduct, array $productIds): void
	{
		$mainTaxonAction = $request->request->get('main_taxon_action');
		$taxonAction = $request->request->get('taxons_action');

		assert($mainTaxonAction !== null);
		assert($taxonAction !== null);

		foreach ($productIds as $productId) {
			$product = $this->productRepository->find($productId);
			assert($product instanceof ProductInterface);

			$this->manageMainTaxon($product, $dummyProduct, $mainTaxonAction);
			$this->manageCategories($product, $dummyProduct, $taxonAction);
		}

		$this->entityManager->flush();
	}

	protected function manageMainTaxon(ProductInterface $product, ProductInterface $dummyProduct, string $action): void
	{
		if ($action === 'replace') {
			$product->setMainTaxon($dummyProduct->getMainTaxon());

			return;
		}

		if ($action === 'add') {
			if ($product->getMainTaxon() === null) {
				$product->setMainTaxon($dummyProduct->getMainTaxon());
			}

			return;
		}

		if ($action === 'remove') {
			if ($product->getMainTaxon() === $dummyProduct->getMainTaxon()) {
				$product->setMainTaxon(null);
			}

			return;
		}

		if ($action === 'remove_all') {
			$product->setMainTaxon(null);

			return;
		}
	}

	protected function manageCategories(ProductInterface $product, ProductInterface $dummyProduct, string $action): void
	{
		if ($action === 'replace') {
			$product->getProductTaxons()->clear();
			$this->entityManager->flush();
			$this->addTaxon($product, $dummyProduct);

			return;
		}

		if ($action === 'add') {
			$this->addTaxon($product, $dummyProduct);

			return;
		}

		if ($action === 'remove') {
			$this->removeTaxon($product, $dummyProduct);

			return;
		}

		if ($action === 'remove_all') {
			$product->getProductTaxons()->clear();

			return;
		}
	}

	protected function addTaxon(ProductInterface $product, ProductInterface $dummyProduct): void
	{
		foreach ($dummyProduct->getProductTaxons() as $productTaxon) {
			$taxon = $productTaxon->getTaxon();
			if ($taxon !== null && !$product->hasTaxon($taxon)) {
				$dummyProductTaxon = $this->productTaxonFactory->createNew();
				assert($dummyProductTaxon instanceof ProductTaxonInterface);
				$dummyProductTaxon->setProduct($product);
				$dummyProductTaxon->setTaxon($taxon);

				$product->addProductTaxon($dummyProductTaxon);
			}
		}
	}

	protected function removeTaxon(ProductInterface $product, ProductInterface $dummyProduct): void
	{
		foreach ($dummyProduct->getProductTaxons() as $productTaxon) {
			$taxon = $productTaxon->getTaxon();
			if ($taxon !== null && $product->hasTaxon($taxon)) {
				foreach ($product->getProductTaxons() as $pt) {
					if ($pt->getTaxon() === $taxon) {
						$product->removeProductTaxon($pt);

						break;
					}
				}
			}
		}
	}
}
