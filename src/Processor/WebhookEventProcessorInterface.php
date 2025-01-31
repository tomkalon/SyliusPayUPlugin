<?php

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\Processor;

use Stripe\Event;
use Sylius\Component\Payment\Model\PaymentRequestInterface;

interface WebhookEventProcessorInterface
{
    public function process(PaymentRequestInterface $paymentRequest, Event $event): void;

    public function supports(PaymentRequestInterface $paymentRequest, Event $event): bool;
}
