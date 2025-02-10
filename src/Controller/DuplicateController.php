<?php

declare(strict_types=1);

namespace MangoSylius\ExtendedChannelsPlugin\Controller;

use Liip\ImagineBundle\Exception\Config\Filter\NotFoundException;
use MangoSylius\ExtendedChannelsPlugin\Service\ProductDuplicatorInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class DuplicateController
{
    /** @var RouterInterface */
    private $router;

    /** @var FlashBagInterface */
    private $flashBag;

    /** @var TranslatorInterface */
    private $translator;

    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var ProductVariantRepositoryInterface */
    private $productVariantRepository;

    /** @var ProductDuplicatorInterface */
    private $productDuplicator;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        TranslatorInterface $translator,
        FlashBagInterface $flashBag,
        RouterInterface $router,
        ProductRepositoryInterface $productRepository,
        ProductVariantRepositoryInterface $productVariantRepository,
        ProductDuplicatorInterface $productDuplicator,
    ) {
        $this->router = $router;
        $this->flashBag = $flashBag;
        $this->translator = $translator;
        $this->productRepository = $productRepository;
        $this->productVariantRepository = $productVariantRepository;
        $this->productDuplicator = $productDuplicator;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function duplicateProduct(int $id): RedirectResponse
    {
        $entity = $this->productRepository->find($id);
        if ($entity === null) {
            throw new NotFoundException();
        }

        assert($entity instanceof ProductInterface);
        $clonedEntity = $this->productDuplicator->duplicateProduct($entity);

        $event = new GenericEvent($clonedEntity, ['oldEntity' => $entity]);
        $this->eventDispatcher->dispatch($event, 'mango-sylius-extended-channels.duplicate.product.before-persist');
        $this->productRepository->add($clonedEntity);
        $this->eventDispatcher->dispatch($event, 'mango-sylius-extended-channels.duplicate.product.after-persist');

        $message = $this->translator->trans('mango-sylius.admin.product.success');
        $this->flashBag->add('success', $message);

        return new RedirectResponse($this->router->generate('sylius_admin_product_update', ['id' => $clonedEntity->getId()]));
    }

    public function duplicateProductVariant(int $id): RedirectResponse
    {
        $entity = $this->productVariantRepository->find($id);
        if ($entity === null) {
            throw new NotFoundException();
        }

        assert($entity instanceof ProductVariantInterface);
        $product = $entity->getProduct();
        assert($product instanceof ProductInterface);
        $clonedEntity = $this->productDuplicator->duplicateProductVariant($product, $entity);

        $event = new GenericEvent($clonedEntity);
        $this->eventDispatcher->dispatch($event, 'mango-sylius-extended-channels.duplicate.product-variant.before-persist');
        $this->productVariantRepository->add($clonedEntity);
        $this->eventDispatcher->dispatch($event, 'mango-sylius-extended-channels.duplicate.product-variant.after-persist');

        $message = $this->translator->trans('mango-sylius.admin.product_variant.success');
        $this->flashBag->add('success', $message);

        return new RedirectResponse($this->router->generate('sylius_admin_product_variant_update', [
            'id' => $clonedEntity->getId(),
            'productId' => $product->getId(),
        ]));
    }
}
