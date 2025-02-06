<?php

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\Manager;

use BitBag\SyliusPayUPlugin\Provider\OptsProviderInterface;
use BitBag\SyliusPayUPlugin\Provider\ParamsProviderInterface;
use Stripe\ApiResource;
use Stripe\Service\AbstractService;
use Sylius\Component\Payment\Model\PaymentRequestInterface;

/**
 * @template T as AbstractService
 * @template O as ApiResource
 */
trait CreateManagerTrait
{
    /** @use StripeClientAwareManagerTrait<T> */
    use StripeClientAwareManagerTrait;

    /**
     * @var ParamsProviderInterface<O> $paramsProvider
     */
    private readonly ParamsProviderInterface $paramsProvider;

    private ?OptsProviderInterface $optsProvider = null;

    public function create(PaymentRequestInterface $paymentRequest): ApiResource
    {
        $stripeClient = $this->getStripeClient($paymentRequest);

        $params = $this->paramsProvider->getParams($paymentRequest);
        $opts = $this->optsProvider?->getOpts($paymentRequest);

        return $this->getService($stripeClient)->create($params, $opts);
    }
}
