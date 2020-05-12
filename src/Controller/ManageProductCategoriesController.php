<?php

declare(strict_types=1);

namespace MangoSylius\ExtendedChannelsPlugin\Controller;

use MangoSylius\ExtendedChannelsPlugin\Form\Type\BulkManageProductCategoriesType;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

class ManageProductCategoriesController extends Controller
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
	 * @var TaxonRepositoryInterface
	 */
	private $taxonRepository;

	public function __construct(
		TranslatorInterface $translator,
		FlashBagInterface $flashBag,
		RouterInterface $router,
		ProductRepositoryInterface $productRepository,
		EngineInterface $templatingEngine,
		TaxonRepositoryInterface $taxonRepository
	) {
		$this->router = $router;
		$this->flashBag = $flashBag;
		$this->translator = $translator;
		$this->productRepository = $productRepository;
		$this->templatingEngine = $templatingEngine;
		$this->taxonRepository = $taxonRepository;
	}

	public function bulkManageProductCategories(Request $request): Response
	{
		$productIds = array_filter(explode(',', $request->get('bulkProductsIds')));
		if (!$productIds) {
			return new RedirectResponse($this->router->generate('sylius_admin_product_index'));
		}

		if ($request->isMethod('POST')) {
			foreach ($productIds as $productId) {
				/** @var ProductInterface $product */
				$product = $this->productRepository->find($productId);
				$product->getProductTaxons()->clear();
				$form = $this->createForm(BulkManageProductCategoriesType::class, $product);
				$form->handleRequest($request);
			}
			$this->getDoctrine()->getManager()->flush();

			$message = $this->translator->trans('mango-sylius.admin.manage_product_categories.saved');
			$this->flashBag->add('success', $message);

			return new RedirectResponse($this->router->generate('sylius_admin_product_index'));
		}

		/** @var ProductInterface $mainProduct */
		$mainProduct = $this->productRepository->find(current($productIds));
		$mainProduct->getProductTaxons()->clear();
		$form = $this->createForm(BulkManageProductCategoriesType::class, $mainProduct);

		return $this->templatingEngine->renderResponse('@MangoSyliusExtendedChannelsPlugin/ManageProductCategories/form.html.twig', [
			'form' => $form->createView(),
			'productIds' => implode(',', $productIds),
			'paths' => [
				'cancel' => $this->router->generate('sylius_admin_product_index'),
			],
		]);
	}
}
