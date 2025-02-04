<?php

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\Provider\Checkout\Create;

use BitBag\SyliusPayUPlugin\Provider\AfterUrlProviderInterface;
use BitBag\SyliusPayUPlugin\Provider\InnerParamsProviderInterface;
use Stripe\Checkout\Session;
use Sylius\Component\Payment\Model\PaymentRequestInterface;

/**
 * @implements InnerParamsProviderInterface<Session>
 */
final readonly class AfterUrlProvider implements InnerParamsProviderInterface
{
    public function __construct(
        private AfterUrlProviderInterface $defaultAfterPayUrlProvider,
    ) {
    }

    public function provide(PaymentRequestInterface $paymentRequest, array &$params): void
    {
        /** @var string[] $payload */
        $payload = $paymentRequest->getPayload();

        foreach ([
            AfterUrlProviderInterface::SUCCESS_URL,
            AfterUrlProviderInterface::CANCEL_URL,
        ] as $type) {
            $params[$type] = $payload[$type] ?? $this->defaultAfterPayUrlProvider->getUrl($paymentRequest, $type);
        }
    }
}
