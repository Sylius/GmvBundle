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

use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatterInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Webmozart\Assert\Assert;

final class GmvProvider implements GmvProviderInterface
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly MoneyFormatterInterface $moneyFormatter
    ) {
    }

    /** @return array<string, string> */
    public function getGmvForPeriod(\DateTimeInterface $periodStart, \DateTimeInterface $periodEnd): array
    {
        $gmv = [];
        $currencyCodes = $this->findCurrenciesInOrders($periodStart, $periodEnd);

        foreach ($currencyCodes as $currencyCode) {
            $total = $this->calculateGmvForPeriodAndCurrency($periodStart, $periodEnd, $currencyCode);

            $gmv[$currencyCode] = $this->moneyFormatter->format($total, $currencyCode);
        }

        return $gmv;
    }

    /** @return array<string> */
    private function findCurrenciesInOrders(\DateTimeInterface $periodStart, \DateTimeInterface $periodEnd): array
    {
        $queryBuilder = $this->orderRepository->createQueryBuilder('o');

        $query = $queryBuilder
            ->select('o.currencyCode')
            ->andWhere('o.checkoutCompletedAt >= :periodStart')
            ->andWhere('o.checkoutCompletedAt <= :periodEnd')
            ->andWhere('o.checkoutState = :completedState')
            ->andWhere('o.paymentState != :cancelledState')
            ->setParameter('periodStart', $periodStart)
            ->setParameter('periodEnd', $periodEnd)
            ->setParameter('completedState', OrderCheckoutStates::STATE_COMPLETED)
            ->setParameter('cancelledState', OrderPaymentStates::STATE_CANCELLED)
            ->groupBy('o.currencyCode')
            ->getQuery();

        $currencies = $query->getScalarResult();

        Assert::isArray($currencies);

        return array_map(fn(array $currency) => $currency['currencyCode'], $currencies);
    }

    private function calculateGmvForPeriodAndCurrency(\DateTimeInterface $periodStart, \DateTimeInterface $periodEnd, string $currencyCode): int
    {
        $queryBuilder = $this->orderRepository->createQueryBuilder('o');

        $totalItemsQuery = $queryBuilder
            ->select('SUM(o.itemsTotal) as totalItems')
            ->andWhere('o.checkoutCompletedAt >= :periodStart')
            ->andWhere('o.checkoutCompletedAt <= :periodEnd')
            ->andWhere('o.checkoutState = :completedState')
            ->andWhere('o.paymentState != :cancelledState')
            ->andWhere('o.currencyCode = :currencyCode')
            ->setParameter('periodStart', $periodStart)
            ->setParameter('periodEnd', $periodEnd)
            ->setParameter('completedState', OrderCheckoutStates::STATE_COMPLETED)
            ->setParameter('cancelledState', OrderPaymentStates::STATE_CANCELLED)
            ->setParameter('currencyCode', $currencyCode)
            ->getQuery()
            ->getSingleScalarResult();

        $totalTaxQuery = $queryBuilder
            ->select('SUM(adjustment.amount) as totalTaxes')
            ->leftJoin('o.items', 'items')
            ->leftJoin('items.units', 'units')
            ->leftJoin('units.adjustments', 'adjustment', 'WITH', 'adjustment.type = :taxType AND adjustment.neutral = true')
            ->andWhere('o.checkoutCompletedAt >= :periodStart')
            ->andWhere('o.checkoutCompletedAt <= :periodEnd')
            ->andWhere('o.checkoutState = :completedState')
            ->andWhere('o.paymentState != :cancelledState')
            ->andWhere('o.currencyCode = :currencyCode')
            ->setParameter('periodStart', $periodStart)
            ->setParameter('periodEnd', $periodEnd)
            ->setParameter('completedState', OrderCheckoutStates::STATE_COMPLETED)
            ->setParameter('cancelledState', OrderPaymentStates::STATE_CANCELLED)
            ->setParameter('taxType', 'tax')
            ->setParameter('currencyCode', $currencyCode)
            ->getQuery()
            ->getSingleScalarResult();

        $totalItems = intval($totalItemsQuery);
        $totalTaxes = intval($totalTaxQuery);

        return $totalItems - $totalTaxes;
    }
}
