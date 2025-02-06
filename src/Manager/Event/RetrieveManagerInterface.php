<?php

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\Manager\Event;

use BitBag\SyliusPayUPlugin\Manager\RetrieveManagerInterface as BaseRetrieveManagerInterface;
use Stripe\Event;

/**
 * @extends BaseRetrieveManagerInterface<Event>
 */
interface RetrieveManagerInterface extends BaseRetrieveManagerInterface
{
}
