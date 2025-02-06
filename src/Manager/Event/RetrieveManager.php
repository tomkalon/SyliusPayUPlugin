<?php

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\Manager\Event;

use BitBag\SyliusPayUPlugin\Manager\RetrieveManagerTrait;
use BitBag\SyliusPayUPlugin\Provider\OptsProviderInterface;
use BitBag\SyliusPayUPlugin\Provider\ParamsProviderInterface;
use BitBag\SyliusPayUPlugin\Stripe\Factory\ClientFactoryInterface;
use Stripe\Event;
use Stripe\Service\EventService;

final class RetrieveManager implements RetrieveManagerInterface
{
    use EventServiceAwareTrait;

    /** @use RetrieveManagerTrait<EventService, Event> */
    use RetrieveManagerTrait;

    /**
     * @param ParamsProviderInterface<Event>|null $paramsProvider
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
