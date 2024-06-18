<?php

namespace Tests\Sylius\GmvBundle\Command;

use Sylius\GmvBundle\Command\GmvCommand;
use Sylius\GmvBundle\Parser\DateParserInterface;
use Sylius\GmvBundle\Provider\GmvProviderInterface;
use Sylius\GmvBundle\Validator\InputParametersValidatorInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class GmvCommandTest extends TestCase
{
    private InputParametersValidatorInterface $validator;
    private DateParserInterface $dateParser;
    private GmvProviderInterface $gmvProvider;
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        $this->validator = $this->createMock(InputParametersValidatorInterface::class);
        $this->dateParser = $this->createMock(DateParserInterface::class);
        $this->gmvProvider = $this->createMock(GmvProviderInterface::class);

        $command = new GmvCommand($this->validator, $this->dateParser, $this->gmvProvider);

        $application = new Application();
        $application->add($command);

        $command = $application->find('sylius:gmv:calculate');
        $this->commandTester = new CommandTester($command);
    }

    private function mockDefaultValues(): void
    {
        $defaultStartDate = new \DateTime('first day of last month');
        $defaultEndDate = new \DateTime('last day of last month');

        $this->dateParser
            ->method('getDefaultStartDate')
            ->willReturn($defaultStartDate);

        $this->dateParser
            ->method('getDefaultEndDate')
            ->willReturn($defaultEndDate);

        $this->dateParser
            ->method('parseStartOfMonth')
            ->willReturn($defaultStartDate);

        $this->dateParser
            ->method('parseEndOfMonth')
            ->willReturn($defaultEndDate);
    }

    public function testExecuteWithDefaultValues(): void
    {
        $this->validator
            ->method('validate')
            ->willReturn(true);

        $this->mockDefaultValues();

        $this->gmvProvider
            ->method('getGmvForPeriod')
            ->willReturn('$1000.00');

        $this->commandTester->execute([]);

        $this->commandTester->assertCommandIsSuccessful();

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('GMV Calculation', $output);
        $this->assertStringContainsString('Period Start:', $output);
        $this->assertStringContainsString('Period End:', $output);
        $this->assertStringContainsString('GMV: $1000.00', $output);
    }

    public function testExecuteWithCustomValues(): void
    {
        $this->validator
            ->method('validate')
            ->willReturn(true);

        $periodStart = '01/2023';
        $periodEnd = '02/2023';

        $startDate = new \DateTime('2023-01-01');
        $endDate = new \DateTime('2023-02-28');

        $this->dateParser
            ->method('parseStartOfMonth')
            ->with($periodStart)
            ->willReturn($startDate);

        $this->dateParser
            ->method('parseEndOfMonth')
            ->with($periodEnd)
            ->willReturn($endDate);

        $this->gmvProvider
            ->method('getGmvForPeriod')
            ->with($startDate, $endDate)
            ->willReturn('$2000.00');

        $this->commandTester->execute([
            'periodStart' => $periodStart,
            'periodEnd' => $periodEnd,
        ]);

        $this->commandTester->assertCommandIsSuccessful();

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('GMV Calculation', $output);
        $this->assertStringContainsString('Period Start:', $output);
        $this->assertStringContainsString('Period End:', $output);
        $this->assertStringContainsString('GMV: $2000.00', $output);
    }

    public function testExecuteWithInvalidFormatDates(): void
    {
        $this->validator
            ->method('validate')
            ->willReturn(false);

        $this->commandTester->execute([
            'periodStart' => '2023-01-01',
            'periodEnd' => 'invalidDate',
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Invalid format or start date must be less than end date. Please use MM/YYYY.', $output);
    }

    public function testExecuteWithStartDateLaterThenEndDate(): void
    {
        $this->validator
            ->method('validate')
            ->willReturn(false);

        $this->commandTester->execute([
            'periodStart' => '05/2024',
            'periodEnd' => '04/2024',
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Invalid format or start date must be less than end date. Please use MM/YYYY.', $output);
    }
}
