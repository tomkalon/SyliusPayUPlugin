<?php

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\CommandHandler;

use BitBag\SyliusPayUPlugin\Command\RefundPaymentRequest;
use BitBag\SyliusPayUPlugin\Manager\Checkout\RetrieveManagerInterface;
use BitBag\SyliusPayUPlugin\Manager\Refund\CreateManagerInterface;
use BitBag\SyliusPayUPlugin\Processor\PaymentTransitionProcessorInterface;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\PaymentBundle\Provider\PaymentRequestProviderInterface;
use Sylius\Component\Payment\PaymentRequestTransitions;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class RefundPaymentRequestHandler
{
    use FailedAwarePaymentRequestHandlerTrait;

    public function __construct(
        private PaymentRequestProviderInterface $paymentRequestProvider,
        private RetrieveManagerInterface $retrieveCheckoutManager,
        private CreateManagerInterface $createRefundManager,
        private PaymentTransitionProcessorInterface $paymentTransitionProcessor,
        StateMachineInterface $stateMachine,
    ) {
        $this->stateMachine = $stateMachine;
    }

    public function __invoke(RefundPaymentRequest $refundPaymentRequest): void
    {
        $paymentRequest = $this->paymentRequestProvider->provide($refundPaymentRequest);

        /** @var string|null $id */
        $id = $paymentRequest->getPayment()->getDetails()['id'] ?? null;
        if (null === $id) {
            $this->failWithReason(
                $paymentRequest,
                'An id is required to retrieve the related PayU Checkout/Session.'
            );
            return;
        }

        $session = $this->retrieveCheckoutManager->retrieve($paymentRequest, $id);
        if ($session::PAYMENT_STATUS_PAID !== $session->payment_status) {
            $this->failWithReason(
                $paymentRequest,
                sprintf(
                    'Checkout Session payment status is "%s" instead of "%s".',
                    $session->payment_status,
                    $session::PAYMENT_STATUS_PAID,
                )
            );
            return;
        }

        if (0 >= $session->amount_total) {
            $this->failWithReason(
                $paymentRequest,
                sprintf(
                    'Checkout Session amount total is not greater than 0 (amount_total: %s)',
                    $session->amount_total,
                )
            );

            return;
        }

        $paymentRequest->setPayload([
            'payment_intent' => $session->payment_intent,
            'amount' => $refundPaymentRequest->getAmount(),
        ]);

        $refund = $this->createRefundManager->create($paymentRequest);

        $paymentRequest->setResponseData($refund->toArray());

        $paymentRequest->getPayment()->setDetails($session->toArray());

        $this->paymentTransitionProcessor->process($paymentRequest);

        $this->stateMachine->apply(
            $paymentRequest,
            PaymentRequestTransitions::GRAPH,
            PaymentRequestTransitions::TRANSITION_COMPLETE,
        );
    }
}
