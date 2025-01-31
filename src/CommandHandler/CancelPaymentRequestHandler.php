<?php

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\CommandHandler;

use BitBag\SyliusPayUPlugin\Command\CancelPaymentRequest;
use BitBag\SyliusPayUPlugin\Manager\Checkout\ExpireManagerInterface;
use BitBag\SyliusPayUPlugin\Manager\Checkout\RetrieveManagerInterface;
use BitBag\SyliusPayUPlugin\Processor\PaymentTransitionProcessorInterface;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\PaymentBundle\Provider\PaymentRequestProviderInterface;
use Sylius\Component\Payment\PaymentRequestTransitions;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class CancelPaymentRequestHandler
{
    use FailedAwarePaymentRequestHandlerTrait;

    public function __construct(
        private PaymentRequestProviderInterface $paymentRequestProvider,
        private RetrieveManagerInterface $retrieveCheckoutManager,
        private ExpireManagerInterface $expireCheckoutManager,
        private PaymentTransitionProcessorInterface $paymentTransitionProcessor,
        StateMachineInterface $stateMachine,
    ) {
        $this->stateMachine = $stateMachine;
    }

    public function __invoke(CancelPaymentRequest $cancelPaymentRequest): void
    {
        $paymentRequest = $this->paymentRequestProvider->provide($cancelPaymentRequest);

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
        if ($session::STATUS_OPEN !== $session->status) {
            return;
        }

        $session = $this->expireCheckoutManager->expire($paymentRequest, $id);

        $paymentRequest->getPayment()->setDetails($session->toArray());

        $this->paymentTransitionProcessor->process($paymentRequest);

        $this->stateMachine->apply(
            $paymentRequest,
            PaymentRequestTransitions::GRAPH,
            PaymentRequestTransitions::TRANSITION_COMPLETE,
        );
    }
}
