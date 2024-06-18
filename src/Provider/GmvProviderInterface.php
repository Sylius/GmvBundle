<?php

namespace Sylius\GmvBundle\Provider;

interface GmvProviderInterface
{
    public function getGmvForPeriod(\DateTimeInterface $periodStart, \DateTimeInterface $periodEnd): string;
}
