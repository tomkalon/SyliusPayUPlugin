<?php

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\Manager;

use Stripe\ApiResource;
use Sylius\Component\Payment\Model\PaymentRequestInterface;

/**
 * @template T as ApiResource
 */
interface RetrieveManagerInterface
{
    /**
     * @return T
     */
    public function retrieve(PaymentRequestInterface $paymentRequest, string $id): ApiResource;
}
