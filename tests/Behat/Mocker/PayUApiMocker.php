<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusPayUPlugin\Behat\Mocker;

use BitBag\SyliusPayUPlugin\Bridge\OpenPayUBridge;
use BitBag\SyliusPayUPlugin\Bridge\OpenPayUBridgeInterface;
use Mockery;
use OpenPayU_Result;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class PayUApiMocker
{
    private ContainerInterface $container;
    private ?Mockery\MockInterface $mockedService = null;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @Given /^the PayU API will return a successful payment response$/
     */
    public function mockApiSuccessfulPaymentResponse(): void
    {
        $mock = Mockery::mock(OpenPayUBridgeInterface::class);
        $mock->shouldReceive('create')->andReturn($this->createResponseSuccessfulApi());
        $mock->shouldReceive('setAuthorizationData')->andReturnNull();

        $this->container->set('bitbag.payu_plugin.bridge.open_payu', $mock);
    }

    public function completedPayment(callable $action): void
    {
        $this->mockService(OpenPayUBridgeInterface::class, [
            'retrieve' => $this->getDataRetrieve(OpenPayUBridge::COMPLETED_API_STATUS),
            'create' => $this->createResponseSuccessfulApi(),
            'setAuthorizationData' => null,
        ]);

        $action();

        $this->unmockAll();
    }

    public function canceledPayment(callable $action): void
    {
        $this->mockService(OpenPayUBridgeInterface::class, [
            'retrieve' => $this->getDataRetrieve(OpenPayUBridge::CANCELED_API_STATUS),
            'create' => $this->createResponseSuccessfulApi(),
            'setAuthorizationData' => null,
        ]);

        $action();

        $this->unmockAll();
    }

    private function mockService(string $serviceId, array $methods): void
    {
        $mock = Mockery::mock($serviceId);

        foreach ($methods as $method => $returnValue) {
            $mock->shouldReceive($method)->andReturn($returnValue);
        }

        $this->mockedService = $mock;
        $this->container->set('bitbag.payu_plugin.bridge.open_payu', $mock);
    }

    private function unmockAll(): void
    {
        if ($this->mockedService !== null) {
            Mockery::close();
            $this->mockedService = null;
        }
    }

    private function getDataRetrieve(string $statusPayment): OpenPayU_Result
    {
        $openPayUResult = new OpenPayU_Result();

        $data = (object) [
            'status' => (object) [
                'statusCode' => OpenPayUBridge::SUCCESS_API_STATUS,
            ],
            'orderId' => 1,
            'orders' => [
                (object) [
                    'status' => $statusPayment,
                ],
            ],
        ];

        $openPayUResult->setResponse($data);

        return $openPayUResult;
    }

    private function createResponseSuccessfulApi(): OpenPayU_Result
    {
        $openPayUResult = new OpenPayU_Result();

        $data = (object) [
            'status' => (object) [
                'statusCode' => OpenPayUBridge::SUCCESS_API_STATUS,
            ],
            'orderId' => 1,
            'redirectUri' => '/',
        ];

        $openPayUResult->setResponse($data);

        return $openPayUResult;
    }
}
