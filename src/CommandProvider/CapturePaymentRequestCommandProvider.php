<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\CommandProvider;

use BitBag\SyliusPayUPlugin\Command\CapturePaymentRequest;
use Sylius\Bundle\PaymentBundle\CommandProvider\PaymentRequestCommandProviderInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;

final readonly class CapturePaymentRequestCommandProvider implements PaymentRequestCommandProviderInterface
{
    public function supports(PaymentRequestInterface $paymentRequest): bool
    {
        return $paymentRequest->getAction() === PaymentRequestInterface::ACTION_CAPTURE;
    }

    public function provide(PaymentRequestInterface $paymentRequest): object
    {
        return new CapturePaymentRequest($paymentRequest->getId());
    }
}
