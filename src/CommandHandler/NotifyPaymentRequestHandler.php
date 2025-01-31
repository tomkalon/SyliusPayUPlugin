<?php

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\CommandHandler;

use BitBag\SyliusPayUPlugin\Command\NotifyPaymentRequest;
use BitBag\SyliusPayUPlugin\Manager\Event\RetrieveManagerInterface;
use BitBag\SyliusPayUPlugin\Processor\PaymentTransitionProcessorInterface;
use BitBag\SyliusPayUPlugin\Processor\WebhookEventProcessorInterface;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\PaymentBundle\Provider\PaymentRequestProviderInterface;
use Sylius\Component\Payment\PaymentRequestTransitions;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class NotifyPaymentRequestHandler
{
    use FailedAwarePaymentRequestHandlerTrait;

    public function __construct(
        private PaymentRequestProviderInterface $paymentRequestProvider,
        private RetrieveManagerInterface $retrieveManager,
        private WebhookEventProcessorInterface $webhookProcessor,
        private PaymentTransitionProcessorInterface $paymentTransitionProcessor,
        StateMachineInterface $stateMachine,
    ) {
        $this->stateMachine = $stateMachine;
    }

    public function __invoke(NotifyPaymentRequest $capturePaymentRequest): void
    {
        $paymentRequest = $this->paymentRequestProvider->provide($capturePaymentRequest);

        /** @var array{event: array<string, mixed>}|null $payload */
        $payload = $paymentRequest->getPayload();
        $data = $payload['event'] ?? [];
        /** @var string|null $id */
        $id = $data['id'] ?? null;
        if (null === $id) {
            $this->failWithReason(
                $paymentRequest,
                'The payment request payload "[event][id]" is null.'
            );
            return;
        }

        $event = $this->retrieveManager->retrieve($paymentRequest, $id);

        $this->webhookProcessor->process($paymentRequest, $event);

        $this->paymentTransitionProcessor->process($paymentRequest);

        $this->stateMachine->apply(
            $paymentRequest,
            PaymentRequestTransitions::GRAPH,
            PaymentRequestTransitions::TRANSITION_COMPLETE,
        );
    }
}
