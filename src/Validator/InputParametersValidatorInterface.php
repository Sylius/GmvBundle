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

namespace Sylius\GmvBundle\Validator;

interface InputParametersValidatorInterface
{
    public function validateDateFormat(string $date): bool;

    public function validateDates(\DateTime $startDate, \DateTime $endDate): bool;

    public function validate(string $periodStart, string $periodEnd): bool;
}
