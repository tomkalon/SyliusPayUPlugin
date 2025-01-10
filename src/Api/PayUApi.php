<?php

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\Api;

use BitBag\SyliusPayUPlugin\Bridge\OpenPayUBridgeInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Webmozart\Assert\Assert;

final readonly class PayUApi implements PayUApiInterface
{
    public function __construct(
        private OpenPayUBridgeInterface $openPayUBridge
    ) {
    }

    public function setApi(PaymentMethodInterface $paymentMethod): void
    {
        $gatewayConfig = $paymentMethod->getGatewayConfig();
        Assert::notNull($gatewayConfig, 'Gateway config cannot be null');

        if ($gatewayConfig ->getFactoryName() !== OpenPayUBridgeInterface::PAYU_PAYMENT_FACTORY_NAME) {
            return;
        }

        $config = $gatewayConfig->getConfig();

        $this->openPayUBridge->setAuthorizationData(
            $config['environment'],
            $config['signature_key'],
            $config['pos_id'],
            $config['oauth_client_id'],
            $config['oauth_client_secret'],
        );
    }
}
