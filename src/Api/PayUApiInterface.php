<?php

namespace BitBag\SyliusPayUPlugin\Api;

use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;

interface PayUApiInterface
{
    public function setApi(PaymentMethodInterface $paymentMethod): void;

    public function prepareOrder(PaymentInterface $payment): array;
}
