<?php

namespace Sylius\GmvBundle\Parser;

interface DateParserInterface
{
    public function parseStartOfMonth(string $date): \DateTime;

    public function parseEndOfMonth(string $date): \DateTime;

    public function getDefaultStartDate(): \DateTime;

    public function getDefaultEndDate(): \DateTime;
}
