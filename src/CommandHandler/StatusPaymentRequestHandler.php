<?php

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\CommandHandler;

use BitBag\SyliusPayUPlugin\Command\StatusPaymentRequest;
use BitBag\SyliusPayUPlugin\Manager\RetrieveManagerInterface;
use BitBag\SyliusPayUPlugin\Processor\PaymentTransitionProcessorInterface;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\PaymentBundle\Provider\PaymentRequestProviderInterface;
use Sylius\Component\Payment\PaymentRequestTransitions;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Webmozart\Assert\Assert;

#[AsMessageHandler]
final readonly class StatusPaymentRequestHandler
{
    use FailedAwarePaymentRequestHandlerTrait;

    /**
     * @param RetrieveManagerInterface $retrieveManager
     */
    public function __construct(
        private PaymentRequestProviderInterface $paymentRequestProvider,
        private RetrieveManagerInterface $retrieveManager,
        private PaymentTransitionProcessorInterface $paymentTransitionProcessor,
        StateMachineInterface $stateMachine,
    ) {
        $this->stateMachine = $stateMachine;
    }

    public function __invoke(StatusPaymentRequest $statusPaymentRequest): void
    {
        $paymentRequest = $this->paymentRequestProvider->provide($statusPaymentRequest);

        /** @var string|null $id */
        $id = $paymentRequest->getPayment()->getDetails()['id'] ?? null;
        if (null === $id) {
            $this->failWithReason(
                $paymentRequest,
                'An id is required to retrieve the related PayU API Resource (Session|PaymentIntent).'
            );
            return;
        }

        $payUApiResource = $this->retrieveManager->retrieve($paymentRequest, $id);

        $paymentRequest->getPayment()->setDetails($payUApiResource->toArray());

        $this->paymentTransitionProcessor->process($paymentRequest);

        $this->stateMachine->apply(
            $paymentRequest,
            PaymentRequestTransitions::GRAPH,
            PaymentRequestTransitions::TRANSITION_COMPLETE,
        );
    }
}
