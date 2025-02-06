<?php

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\Manager\Checkout;

use BitBag\SyliusPayUPlugin\Manager\RetrieveManagerTrait;
use BitBag\SyliusPayUPlugin\Provider\OptsProviderInterface;
use BitBag\SyliusPayUPlugin\Provider\ParamsProviderInterface;
use BitBag\SyliusPayUPlugin\Stripe\Factory\ClientFactoryInterface;
use Stripe\Checkout\Session;
use Stripe\Service\Checkout\SessionService;

final class RetrieveManager implements RetrieveManagerInterface
{
    use CheckoutSessionServiceAwareTrait;

    /** @use RetrieveManagerTrait<SessionService, Session> */
    use RetrieveManagerTrait;

    /**
     * @param ParamsProviderInterface<Session>|null $paramsProvider
     */
    public function __construct(
        ClientFactoryInterface $stripeClientFactory,
        ?ParamsProviderInterface $paramsProvider = null,
        ?OptsProviderInterface $optsProvider = null,
    ) {
        $this->stripeClientFactory = $stripeClientFactory;
        $this->paramsProvider = $paramsProvider;
        $this->optsProvider = $optsProvider;
    }
}
