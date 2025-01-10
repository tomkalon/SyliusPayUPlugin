<?php

namespace BitBag\SyliusPayUPlugin\Api;

use Sylius\Component\Core\Model\PaymentMethodInterface;

interface PayUApiInterface
{
    public function setApi(PaymentMethodInterface $paymentMethod): void;
}
