<?php

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\Manager\Checkout;

use BitBag\SyliusPayUPlugin\Manager\CreateManagerInterface as BaseCreateManagerInterface;
use Stripe\Checkout\Session;

/**
 * @extends BaseCreateManagerInterface<Session>
 */
interface CreateManagerInterface extends BaseCreateManagerInterface
{
}
