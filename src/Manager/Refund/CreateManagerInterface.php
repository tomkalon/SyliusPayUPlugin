<?php

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\Manager\Refund;

use BitBag\SyliusPayUPlugin\Manager\CreateManagerInterface as BaseCreateManagerInterface;
use Stripe\Refund;

/**
 * @extends BaseCreateManagerInterface<Refund>
 */
interface CreateManagerInterface extends BaseCreateManagerInterface
{
}
