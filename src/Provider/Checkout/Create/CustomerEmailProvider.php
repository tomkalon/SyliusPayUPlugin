<?php

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\Provider\Checkout\Create;

use BitBag\SyliusPayUPlugin\Provider\InnerParamsProviderInterface;
use Stripe\Checkout\Session;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;

/**
 * @implements InnerParamsProviderInterface<Session>
 */
final readonly class CustomerEmailProvider implements InnerParamsProviderInterface
{
    public function provide(PaymentRequestInterface $paymentRequest, array &$params): void
    {
        /** @var PaymentInterface $payment */
        $payment = $paymentRequest->getPayment();
        $order = $payment->getOrder();

        $email = $order?->getCustomer()?->getEmail();

        if(null === $email) {
            return;
        }

        $params['customer_email'] = $email;
    }
}
