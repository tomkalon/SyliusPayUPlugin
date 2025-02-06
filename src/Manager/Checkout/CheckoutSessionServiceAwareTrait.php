<?php

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\Manager\Checkout;

use Stripe\Service\AbstractService;
use Stripe\Service\Checkout\SessionService;
use Stripe\StripeClient;

trait CheckoutSessionServiceAwareTrait
{
    /**
     * @return SessionService
     */
    private function getService(StripeClient $stripeClient): AbstractService
    {
        return $stripeClient->checkout->sessions;
    }
}
