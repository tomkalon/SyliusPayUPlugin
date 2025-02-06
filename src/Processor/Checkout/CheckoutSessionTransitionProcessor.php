<?php

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\Processor\Checkout;

use BitBag\SyliusPayUPlugin\Bridge\OpenPayUBridge;
use BitBag\SyliusPayUPlugin\Processor\PaymentTransitionProcessorInterface;
use OpenPayU_Order;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\PaymentTransitions;

final readonly class CheckoutSessionTransitionProcessor implements PaymentTransitionProcessorInterface
{
    public function __construct(
        private StateMachineInterface $stateMachine,
        private OpenPayUBridge  $openPayUBridge,
    ) {
    }

    public function process(PaymentRequestInterface $paymentRequest): void
    {
        $payment = $paymentRequest->getPayment();
        $details = $payment->getDetails();
        $payUResult = $this->openPayUBridge->retrieve($details['orderId']);


        $transition = $this->getTransition($payUResult);

        if (null === $transition) {
            return;
        }

        if ($this->stateMachine->can($payment, PaymentTransitions::GRAPH, $transition)) {
            $this->stateMachine->apply($payment, PaymentTransitions::GRAPH, $transition);
        }
    }

    private function getTransition(\OpenPayU_Result $payUResult): ?string
    {
        $status = $payUResult->getSuccess();
        $paymentStatus = $payUResult->getStatus();

        if ($this->isCompleteStatus($status, $paymentStatus)) {
            return PaymentTransitions::TRANSITION_COMPLETE;
        }

        if ($this->isFailStatus($status)) {
            return PaymentTransitions::TRANSITION_FAIL;
        }

        if ($this->isProcessStatus($status, $paymentStatus)) {
            return PaymentTransitions::TRANSITION_PROCESS;
        }

        return null;
    }

    private function isCompleteStatus(?bool $status, string $paymentStatus): bool
    {

        if (true !== $status) {
            return false;
        }

        return Session::PAYMENT_STATUS_UNPAID !== $paymentStatus;
    }

    private function isFailStatus(?string $status): bool
    {
        return Session::STATUS_EXPIRED === $status;
    }

    private function isProcessStatus(?string $status, string $paymentStatus): bool
    {
//        if (Session::STATUS_COMPLETE !== $status) {
//            return false;
//        }

        return Session::PAYMENT_STATUS_UNPAID === $paymentStatus;
    }
}
