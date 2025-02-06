<?php

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\Manager\Checkout;

use BitBag\SyliusPayUPlugin\Manager\StripeClientAwareManagerTrait;
use BitBag\SyliusPayUPlugin\Provider\OptsProviderInterface;
use BitBag\SyliusPayUPlugin\Provider\ParamsProviderInterface;
use BitBag\SyliusPayUPlugin\Stripe\Factory\ClientFactoryInterface;
use Stripe\Checkout\Session;
use Stripe\Service\Checkout\SessionService;
use Sylius\Component\Payment\Model\PaymentRequestInterface;

final class ExpireManager implements ExpireManagerInterface
{
    use CheckoutSessionServiceAwareTrait;

    /** @use StripeClientAwareManagerTrait<SessionService> */
    use StripeClientAwareManagerTrait;

    /**
     * @param ParamsProviderInterface<Session>|null $paramsProvider
     */
    public function __construct(
        ClientFactoryInterface $stripeClientFactory,
        private ?ParamsProviderInterface $paramsProvider = null,
        private ?OptsProviderInterface $optsProvider = null,
    ) {
        $this->stripeClientFactory = $stripeClientFactory;
    }

    public function expire(PaymentRequestInterface $paymentRequest, string $id): Session
    {
        $stripeClient = $this->getStripeClient($paymentRequest);

        $params = $this->paramsProvider?->getParams($paymentRequest);
        $opts = $this->optsProvider?->getOpts($paymentRequest);

        return $this->getService($stripeClient)->expire($id, $params, $opts);
    }
}
