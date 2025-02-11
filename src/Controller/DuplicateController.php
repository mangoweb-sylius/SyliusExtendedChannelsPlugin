<?php

declare(strict_types=1);

namespace MangoSylius\ExtendedChannelsPlugin\Controller;

use Liip\ImagineBundle\Exception\Config\Filter\NotFoundException;
use MangoSylius\ExtendedChannelsPlugin\Controller\Partials\GetFlashBagTrait;
use MangoSylius\ExtendedChannelsPlugin\Service\ProductDuplicatorInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class DuplicateController
{
    use GetFlashBagTrait;

    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private TranslatorInterface $translator,
        private RouterInterface $router,
        private ProductRepositoryInterface $productRepository,
        private ProductVariantRepositoryInterface $productVariantRepository,
        private ProductDuplicatorInterface $productDuplicator,
    ) {
    }

    public function duplicateProduct(
        Request $request,
        int $id,
    ): RedirectResponse {
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
        $this->getFlashBag($request)->add('success', $message);

        return new RedirectResponse($this->router->generate('sylius_admin_product_update', ['id' => $clonedEntity->getId()]));
    }

    public function duplicateProductVariant(
        Request $request,
        int $id,
    ): RedirectResponse {
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
        $this->getFlashBag($request)->add('success', $message);

        return new RedirectResponse($this->router->generate('sylius_admin_product_variant_update', [
            'id' => $clonedEntity->getId(),
            'productId' => $product->getId(),
        ]));
    }
}
