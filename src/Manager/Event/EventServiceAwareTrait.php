<?php

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\Manager\Event;

use Stripe\Service\AbstractService;
use Stripe\Service\EventService;
use Stripe\StripeClient;

trait EventServiceAwareTrait
{
    /**
     * @return EventService
     */
    private function getService(StripeClient $stripeClient): AbstractService
    {
        return $stripeClient->events;
    }
}
