<?php

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\Provider\Checkout\Create\LineItem\PriceData;

use BitBag\SyliusPayUPlugin\Provider\Checkout\Create\OrderItemLineItemProviderInterface;
use BitBag\SyliusPayUPlugin\Provider\Checkout\Create\ShipmentLineItemProviderInterface;
use Stripe\LineItem;
use Stripe\StripeObject;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;

/**
 * @implements OrderItemLineItemProviderInterface<LineItem>
 * @implements ShipmentLineItemProviderInterface<LineItem>
 */
final class UnitAmountProvider implements OrderItemLineItemProviderInterface, ShipmentLineItemProviderInterface
{
    public function provideFromOrderItem(
        OrderItemInterface $orderItem,
        PaymentRequestInterface $paymentRequest,
        array &$params
    ): void {
        $this->provide($orderItem->getTotal(), $params);
    }

    public function provideFromShipment(
        ShipmentInterface $shipment,
        PaymentRequestInterface $paymentRequest,
        array &$params
    ): void {
        $this->provide($shipment->getAdjustmentsTotal(), $params);
    }

    /**
     * @param array<key-of<StripeObject>, mixed> $params
     */
    private function provide(int $unitAmount, array &$params): void
    {
        if (0 === $unitAmount) {
            return;
        }

        $params['unit_amount'] = $unitAmount;
    }
}
