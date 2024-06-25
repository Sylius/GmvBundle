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

namespace Sylius\GmvBundle\Command;

use Sylius\GmvBundle\Parser\DateParserInterface;
use Sylius\GmvBundle\Provider\GmvProviderInterface;
use Sylius\GmvBundle\Validator\InputParametersValidatorInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Webmozart\Assert\Assert;

#[AsCommand(
    name: 'sylius:gmv:calculate',
    description: 'Lightweight local command to calculate the Sylius instance GMV within a specific period',
)]
final class GmvCommand extends Command
{
    public function __construct(
        private readonly InputParametersValidatorInterface $validator,
        private readonly DateParserInterface $dateParser,
        private readonly GmvProviderInterface $gmvProvider,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'periodStart',
                InputArgument::OPTIONAL,
                'The start of the period (e.g., 05/2024)',
                $this->dateParser->getDefaultStartDate()->format('m/Y'),
            )
            ->addArgument(
                'periodEnd',
                InputArgument::OPTIONAL,
                'The end of the period (e.g., 06/2024)',
                $this->dateParser->getDefaultEndDate()->format('m/Y'),
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $periodStart = $input->getArgument('periodStart');
        $periodEnd = $input->getArgument('periodEnd');

        Assert::string($periodStart);
        Assert::string($periodEnd);

        if (!$this->validator->validate($periodStart, $periodEnd)) {
            $output->writeln('<error>Invalid format or start date must be less than end date. Please use MM/YYYY.</error>');

            return Command::FAILURE;
        }

        $startDate = $this->dateParser->parseStartOfMonth($periodStart);
        $endDate = $this->dateParser->parseEndOfMonth($periodEnd);

        $gmvs = $this->gmvProvider->getGmvForPeriod($startDate, $endDate);

        $output->writeln('<info>GMV Calculation</info>');
        $output->writeln(sprintf('<comment>Period Start:</comment> %s', $startDate->format('Y-m-d')));
        $output->writeln(sprintf('<comment>Period End:</comment> %s', $endDate->format('Y-m-d')));

        if (empty($gmvs)) {
            $output->writeln('<comment>No sales found for the given period.</comment>');

            return Command::SUCCESS;
        }

        foreach ($gmvs as $key => $gmv) {
            $output->writeln(sprintf('<comment>GMV in %s:</comment> %s', $key, $gmv));
        }

        return Command::SUCCESS;
    }
}
