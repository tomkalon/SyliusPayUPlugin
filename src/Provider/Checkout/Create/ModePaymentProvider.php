<?php

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\Provider\Checkout\Create;

use BitBag\SyliusPayUPlugin\Provider\InnerParamsProviderInterface;
use Stripe\Checkout\Session;
use Sylius\Component\Payment\Model\PaymentRequestInterface;

/**
 * @implements InnerParamsProviderInterface<Session>
 */
final class ModePaymentProvider implements InnerParamsProviderInterface
{
    public function provide(PaymentRequestInterface $paymentRequest, array &$params): void
    {
        $params['mode'] = Session::MODE_PAYMENT;
    }
}
