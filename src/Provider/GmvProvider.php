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
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;

final class GmvProvider implements GmvProviderInterface
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly ChannelContextInterface $channelContext,
        private readonly MoneyFormatterInterface $moneyFormatter
    ) {
    }

    public function getGmvForPeriod(\DateTimeInterface $periodStart, \DateTimeInterface $periodEnd): string
    {
        $total = $this->calculateGmvForPeriod($periodStart, $periodEnd);
        $currencyCode = $this->channelContext->getChannel()->getBaseCurrency()->getCode();

        return $this->moneyFormatter->format($total, $currencyCode);
    }

    private function calculateGmvForPeriod(\DateTimeInterface $periodStart, \DateTimeInterface $periodEnd): int
    {
        $queryBuilder = $this->orderRepository->createListQueryBuilder();

        $queryBuilder
            ->select('sum(o.itemsTotal) as total')
            ->andWhere('o.checkoutCompletedAt >= :periodStart')
            ->andWhere('o.checkoutCompletedAt <= :periodEnd')
            ->andWhere('o.paymentState != :state')
            ->setParameter('periodStart', $periodStart)
            ->setParameter('periodEnd', $periodEnd)
            ->setParameter('state', OrderPaymentStates::STATE_REFUNDED);

        $result = $queryBuilder->getQuery()->getOneOrNullResult();

        return intval($result['total'] ?? 0);
    }
}
