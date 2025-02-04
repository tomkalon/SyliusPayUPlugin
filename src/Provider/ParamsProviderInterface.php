<?php

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\Provider;

use Stripe\StripeObject;
use Sylius\Component\Payment\Model\PaymentRequestInterface;

/**
 * @template T of StripeObject
 */
interface ParamsProviderInterface
{
    /**
     * @return array<key-of<T>, mixed>|null
     */
    public function getParams(PaymentRequestInterface $paymentRequest): ?array;
}
