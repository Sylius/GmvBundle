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

use Sylius\GmvBundle\Parser\DateParserInterface;

final class InputParametersValidator implements InputParametersValidatorInterface
{
    public function __construct(private readonly DateParserInterface $dateParser)
    {
    }

    public function validateDateFormat(string $date): bool
    {
        return preg_match('/^(0[1-9]|1[0-2])\/\d{4}$/', $date) === 1;
    }

    public function validateDates(\DateTime $startDate, \DateTime $endDate): bool
    {
        return $startDate < $endDate;
    }

    public function validate(string $periodStart, string $periodEnd): bool
    {
        if (!$this->validateDateFormat($periodStart) || !$this->validateDateFormat($periodEnd)) {
            return false;
        }

        $startDate = $this->dateParser->parseStartOfMonth($periodStart);
        $endDate = $this->dateParser->parseEndOfMonth($periodEnd);

        return $this->validateDates($startDate, $endDate);
    }
}
