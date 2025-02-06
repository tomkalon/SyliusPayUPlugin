<?php

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\Manager\Refund;

use BitBag\SyliusPayUPlugin\Manager\CreateManagerTrait;
use BitBag\SyliusPayUPlugin\Provider\OptsProviderInterface;
use BitBag\SyliusPayUPlugin\Provider\ParamsProviderInterface;
use BitBag\SyliusPayUPlugin\Stripe\Factory\ClientFactoryInterface;
use Stripe\Refund;
use Stripe\Service\RefundService;

final class CreateManager implements CreateManagerInterface
{
    use RefundServiceAwareTrait;

    /** @use CreateManagerTrait<RefundService, Refund> */
    use CreateManagerTrait;

    /**
     * @param ParamsProviderInterface<Refund> $paramsProvider
     */
    public function __construct(
        ClientFactoryInterface $stripeClientFactory,
        ParamsProviderInterface $paramsProvider,
        ?OptsProviderInterface $optsProvider = null,
    ) {
        $this->stripeClientFactory = $stripeClientFactory;
        $this->paramsProvider = $paramsProvider;
        $this->optsProvider = $optsProvider;
    }
}
