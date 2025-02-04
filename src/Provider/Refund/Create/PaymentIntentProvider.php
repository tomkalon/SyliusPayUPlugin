<?php

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\Provider\Refund\Create;

use BitBag\SyliusPayUPlugin\Provider\InnerParamsProviderInterface;
use Stripe\Refund;
use Sylius\Component\Payment\Model\PaymentRequestInterface;

/**
 * @implements InnerParamsProviderInterface<Refund>
 */
final readonly class PaymentIntentProvider implements InnerParamsProviderInterface
{
    public function provide(PaymentRequestInterface $paymentRequest, array &$params): void
    {
        $payload = $paymentRequest->getPayload();
        if (false === is_array($payload)) {
            return;
        }

        $paymentIntent = $payload['payment_intent'] ?? null;
        if (null === $paymentIntent) {
            return;
        }

        $params['payment_intent'] = $paymentIntent;
    }
}
