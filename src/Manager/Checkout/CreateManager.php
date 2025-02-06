<?php

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\Manager\Checkout;

use BitBag\SyliusPayUPlugin\Manager\CreateManagerTrait;
use BitBag\SyliusPayUPlugin\Provider\OptsProviderInterface;
use BitBag\SyliusPayUPlugin\Provider\ParamsProviderInterface;
use BitBag\SyliusPayUPlugin\Stripe\Factory\ClientFactoryInterface;
use Stripe\Checkout\Session;
use Stripe\Service\Checkout\SessionService;

final class CreateManager implements CreateManagerInterface
{
    use CheckoutSessionServiceAwareTrait;

    /** @use CreateManagerTrait<SessionService, Session> */
    use CreateManagerTrait;

    /**
     * @param ParamsProviderInterface<Session> $paramsProvider
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
