<?php

namespace Sylius\GmvBundle\Provider;

interface GmvProviderInterface
{
    /** @return array<string, string> */
    public function getGmvForPeriod(\DateTimeInterface $periodStart, \DateTimeInterface $periodEnd): array;
}
