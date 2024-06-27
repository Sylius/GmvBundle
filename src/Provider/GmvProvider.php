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

use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatterInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Webmozart\Assert\Assert;

final class GmvProvider implements GmvProviderInterface
{
    /** @param OrderRepositoryInterface<OrderInterface>&EntityRepository $orderRepository */
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly MoneyFormatterInterface $moneyFormatter,
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
        $queryBuilder = $this->createCommonQueryBuilder($periodStart, $periodEnd);
        $queryBuilder->select('o.currencyCode')
            ->groupBy('o.currencyCode');

        $currencies = $queryBuilder->getQuery()->getScalarResult();

        Assert::isArray($currencies);

        return array_map(fn (array $currency) => $currency['currencyCode'], $currencies);
    }

    private function calculateGmvForPeriodAndCurrency(\DateTimeInterface $periodStart, \DateTimeInterface $periodEnd, string $currencyCode): int
    {
        $queryBuilder = $this->createCommonQueryBuilder($periodStart, $periodEnd);
        $queryBuilder->andWhere('o.currencyCode = :currencyCode')
            ->setParameter('currencyCode', $currencyCode);

        $totalItems = (int) $queryBuilder
            ->select('SUM(o.itemsTotal) as totalItems')
            ->getQuery()
            ->getSingleScalarResult();

        $totalTax = (int) $queryBuilder
            ->select('SUM(adjustment.amount) as totalTaxes')
            ->leftJoin('o.items', 'items')
            ->leftJoin('items.units', 'units')
            ->leftJoin('units.adjustments', 'adjustment', 'WITH', 'adjustment.type = :taxType AND adjustment.neutral = true')
            ->setParameter('taxType', AdjustmentInterface::TAX_ADJUSTMENT)
            ->getQuery()
            ->getSingleScalarResult();

        return $totalItems - $totalTax;
    }

    private function createCommonQueryBuilder(\DateTimeInterface $periodStart, \DateTimeInterface $periodEnd): QueryBuilder
    {
        $queryBuilder = $this->orderRepository->createQueryBuilder('o');

        return $queryBuilder
            ->andWhere('o.checkoutCompletedAt >= :periodStart')
            ->andWhere('o.checkoutCompletedAt <= :periodEnd')
            ->andWhere('o.checkoutState = :completedState')
            ->andWhere('o.paymentState != :cancelledState')
            ->setParameter('periodStart', $periodStart)
            ->setParameter('periodEnd', $periodEnd)
            ->setParameter('completedState', OrderCheckoutStates::STATE_COMPLETED)
            ->setParameter('cancelledState', OrderPaymentStates::STATE_CANCELLED);
    }
}
