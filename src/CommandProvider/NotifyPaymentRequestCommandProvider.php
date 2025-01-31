<?php

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\CommandProvider;

use BitBag\SyliusPayUPlugin\Command\NotifyPaymentRequest;
use Sylius\Bundle\PaymentBundle\CommandProvider\PaymentRequestCommandProviderInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;

final class NotifyPaymentRequestCommandProvider implements PaymentRequestCommandProviderInterface
{
    public function supports(PaymentRequestInterface $paymentRequest): bool
    {
        return $paymentRequest->getAction() === PaymentRequestInterface::ACTION_NOTIFY;
    }

    public function provide(PaymentRequestInterface $paymentRequest): object
    {
        return new NotifyPaymentRequest($paymentRequest->getId());
    }
}
