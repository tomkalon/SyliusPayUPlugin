<?php

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\Manager\Checkout;

use BitBag\SyliusPayUPlugin\Manager\RetrieveManagerInterface as BaseRetrieveManagerInterface;
use Stripe\Checkout\Session;

/**
 * @extends BaseRetrieveManagerInterface<Session>
 */
interface RetrieveManagerInterface extends BaseRetrieveManagerInterface
{
}
