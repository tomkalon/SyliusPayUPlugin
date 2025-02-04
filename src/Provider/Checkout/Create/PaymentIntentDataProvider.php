<?php

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\Provider\Checkout\Create;

use BitBag\SyliusPayUPlugin\Provider\InnerParamsProviderInterface;
use Stripe\Checkout\Session;
use Stripe\PaymentIntent;
use Sylius\Component\Payment\Model\PaymentRequestInterface;

/**
 * @implements InnerParamsProviderInterface<Session>
 */
final class PaymentIntentDataProvider implements InnerParamsProviderInterface
{
    /**
     * @param InnerParamsProviderInterface<PaymentIntent>[] $detailsProviders
     */
    public function __construct(
        private iterable $detailsProviders,
    ) {
    }

    public function provide(PaymentRequestInterface $paymentRequest, array &$params): void
    {
        /** @var array<key-of<PaymentIntent>, mixed> $paymentIntentData */
        $paymentIntentData = [];
        foreach ($this->detailsProviders as $detailsProvider) {
            $detailsProvider->provide($paymentRequest, $paymentIntentData);
        }

        if ([] === $paymentIntentData) {
            return;
        }

        if (false === isset($params['payment_intent_data'])) {
            $params['payment_intent_data'] = [];
        }

        $params['payment_intent_data'] += $paymentIntentData;
    }
}
