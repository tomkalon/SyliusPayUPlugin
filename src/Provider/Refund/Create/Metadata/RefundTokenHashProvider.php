<?php

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\Provider\Refund\Create\Metadata;

use BitBag\SyliusPayUPlugin\Provider\InnerParamsProviderInterface;
use BitBag\SyliusPayUPlugin\Provider\MetadataProviderInterface;
use Stripe\Refund;
use Sylius\Component\Payment\Model\PaymentRequestInterface;

/**
 * @implements InnerParamsProviderInterface<Refund>
 */
final readonly class RefundTokenHashProvider implements InnerParamsProviderInterface
{
    public function provide(PaymentRequestInterface $paymentRequest, array &$params): void
    {
        $params[MetadataProviderInterface::REFUND_TOKEN_HASH_KEY_NAME] = $paymentRequest->getId();
    }
}
