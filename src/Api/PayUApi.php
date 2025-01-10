<?php

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\Api;

use BitBag\SyliusPayUPlugin\Bridge\OpenPayUBridgeInterface;
use OpenPayU_Configuration;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Webmozart\Assert\Assert;

final readonly class PayUApi implements PayUApiInterface
{
    public function __construct(
        private OpenPayUBridgeInterface $openPayUBridge,
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

    public function prepareOrder(PaymentInterface $payment): array {
        $payUdata = [];

        /** @var OrderInterface $order */
        $order = $payment->getOrder();

        $payUdata['totalAmount'] = $order->getTotal();
        $payUdata['currencyCode'] = $order->getCurrencyCode();
        $payUdata['tokenValue'] = $order->getTokenValue();
        $payUdata['customerIp'] = $order->getCustomerIp();
        $payUdata['merchantPosId'] = OpenPayU_Configuration::getMerchantPosId();
        $payUdata['description'] = $order->getNumber();

        /** @var CustomerInterface $customer */
        $customer = $order->getCustomer();

        Assert::isInstanceOf(
            $customer,
            CustomerInterface::class,
            sprintf(
                'Make sure the first model is the %s instance.',
                CustomerInterface::class,
            ),
        );

        $buyer = [
            'email' => (string) $customer->getEmail(),
            'firstName' => (string) $customer->getFirstName(),
            'lastName' => (string) $customer->getLastName(),
            'language' => $this->getFallbackLocaleCode($order->getLocaleCode()),
        ];
        $payUdata['buyer'] = $buyer;
        $payUdata['products'] = $this->getOrderItems($order);

        return $payUdata;
    }

    private function getOrderItems(OrderInterface $order): array
    {
        $itemsData = [];

        if ($items = $order->getItems()) {
            /** @var OrderItemInterface $item */
            foreach ($items as $key => $item) {
                $itemsData[$key] = [
                    'name' => $item->getProductName(),
                    'unitPrice' => $item->getUnitPrice(),
                    'quantity' => $item->getQuantity(),
                ];
            }
        }

        return $itemsData;
    }

    private function getFallbackLocaleCode(string $localeCode): string
    {
        return explode('_', $localeCode)[0];
    }
}
