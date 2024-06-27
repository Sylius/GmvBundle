<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\GmvBundle\Provider;

final class DefaultDateProvider implements DefaultDateProviderInterface
{
    public function getDefaultStartDate(): \DateTime
    {
        $now = new \DateTime();

        return $now->modify('first day of -12 months');
    }

    public function getDefaultEndDate(): \DateTime
    {
        $now = new \DateTime();

        return $now->modify('last day of last month');
    }
}
