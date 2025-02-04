<?php

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\Provider\Checkout\Create\LineItem;

use BitBag\SyliusPayUPlugin\Provider\Checkout\Create\OrderItemLineItemProviderInterface;
use BitBag\SyliusPayUPlugin\Provider\Checkout\Create\ShipmentLineItemProviderInterface;
use Stripe\LineItem;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;

/**
 * @implements OrderItemLineItemProviderInterface<LineItem>
 * @implements ShipmentLineItemProviderInterface<LineItem>
 */
final class QuantityProvider implements OrderItemLineItemProviderInterface, ShipmentLineItemProviderInterface
{
    public function provideFromOrderItem(
        OrderItemInterface $orderItem,
        PaymentRequestInterface $paymentRequest,
        array &$params
    ): void {
        $params['quantity'] = 1;
    }

    public function provideFromShipment(
        ShipmentInterface $shipment,
        PaymentRequestInterface $paymentRequest,
        array &$params
    ): void {
        $params['quantity'] = 1;
    }
}
