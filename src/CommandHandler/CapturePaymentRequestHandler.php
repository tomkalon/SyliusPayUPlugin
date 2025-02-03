<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\CommandHandler;

use BitBag\SyliusPayUPlugin\Api\PayUApiInterface;
use BitBag\SyliusPayUPlugin\Bridge\OpenPayUBridge;
use BitBag\SyliusPayUPlugin\Command\CapturePaymentRequest;
use BitBag\SyliusPayUPlugin\Processor\PaymentTransitionProcessorInterface;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\PaymentBundle\Provider\PaymentRequestProviderInterface;
use Sylius\Component\Core\Model\Payment;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\PaymentRequestTransitions;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Webmozart\Assert\Assert;

#[AsMessageHandler]
final readonly class CapturePaymentRequestHandler
{
    public function __construct(
        private PaymentRequestProviderInterface $paymentRequestProvider,
        private StateMachineInterface $stateMachine,
        private PayUApiInterface $api,
        private OpenPayUBridge  $openPayUBridge,
        private PaymentTransitionProcessorInterface $paymentTransitionProcessor,
    ) {}

    public function __invoke(CapturePaymentRequest $capturePaymentRequest): void
    {
        $paymentRequest = $this->paymentRequestProvider->provide($capturePaymentRequest);

        if (PaymentRequestInterface::STATE_PROCESSING === $paymentRequest->getState()) {
            return;
        }

        /** @var Payment $payment */
        $payment = $paymentRequest->getPayment();

        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $payment->getMethod();
        Assert::notNull($paymentMethod, 'Payment method cannot be null');

        $this->api->setApi($paymentMethod);
        $orderData = $this->api->prepareOrder($payment);
        $result = $this->openPayUBridge->create($orderData);
        $response = $result->getResponse();
        $paymentRequest->setResponseData([
            'url' => $response->redirectUri,
        ]);

        $paymentRequest->getPayment()->setDetails((array)$response);

        $this->paymentTransitionProcessor->process($paymentRequest);
        //-------------
//        $url = $response->redirectUri;
//        //-------------
//        $headers['Location'] = $url;

//        $prepareContent = sprintf('<!DOCTYPE html>
//<html>
//    <head>
//        <meta charset="UTF-8" />
//        <meta http-equiv="refresh" content="1;url=%1$s" />
//
//        <title>Redirecting to %1$s</title>
//    </head>
//    <body>
//        Redirecting to %1$s.
//    </body>
//</html>', htmlspecialchars($url, ENT_QUOTES, 'UTF-8'));

        //$this->prepareContent($url), $statusCode, $headers
//        $statusCode = 302;
//        $prepareContent, $statusCode, $headers
        //-----FOR TEST---------
//        header('Location: '.$response->redirectUri);
//        die();
        //--------------------------

        $this->stateMachine->apply(
            $paymentRequest,
            PaymentRequestTransitions::GRAPH,
            PaymentRequestTransitions::TRANSITION_PROCESS,
        );
    }
}
