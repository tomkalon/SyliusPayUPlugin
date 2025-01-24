<?php


declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\OrderPay\Provider;

use Sylius\Bundle\PaymentBundle\Provider\HttpResponseProviderInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class CaptureHttpResponseProvider implements HttpResponseProviderInterface
{
    public function __construct(
        private readonly Environment $twig,
    )
    {
    }

    public function supports(RequestConfiguration $requestConfiguration, PaymentRequestInterface $paymentRequest): bool
    {
        return $paymentRequest->getAction() === PaymentRequestInterface::ACTION_CAPTURE;
    }

    public function getResponse(RequestConfiguration $requestConfiguration, PaymentRequestInterface $paymentRequest): Response
    {
        $data = $paymentRequest->getResponseData();

        // Example: Redirect to an external portal
        return new RedirectResponse($data['portal_redirect_url']);

//        // Example: Display a Twig template
//        return new Response(
//            $this->twig->render(
//                '@AcmeSyliusExamplePlugin/order_pay/capture.html.twig',
//                $data
//            )
//        );
    }
}