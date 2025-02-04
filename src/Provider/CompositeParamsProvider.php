<?php

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\Provider;

use Stripe\ApiResource;
use Stripe\Checkout\Session;
use Sylius\Component\Payment\Model\PaymentRequestInterface;

/**
 * @template T as ApiResource
 * @implements ParamsProviderInterface<T>
 */
final readonly class CompositeParamsProvider implements ParamsProviderInterface
{
    /**
     * @param InnerParamsProviderInterface<T>[] $innerParamsProviders
     */
    public function __construct(
        private iterable $innerParamsProviders,
    ) {
    }

    /**
     * @return array<key-of<Session>, mixed>|null
     */
    public function getParams(PaymentRequestInterface $paymentRequest): ?array
    {
        /** @var array<key-of<T>, mixed> $params */
        $params = [];

        foreach ($this->innerParamsProviders as $innerParamsProvider) {
            $innerParamsProvider->provide($paymentRequest, $params);
        }

        return $params;
    }
}
