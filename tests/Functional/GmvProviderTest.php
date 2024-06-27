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

namespace Tests\Sylius\GmvBundle\Functional;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Sylius\GmvBundle\Provider\GmvProviderInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GmvProviderTest extends KernelTestCase
{
    private GmvProviderInterface $gmvProvider;

    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel(['environment' => 'test']);

        $container = static::getContainer();
        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        $this->gmvProvider = $container->get('sylius_gmv.provider.gmv');

        $this->createDatabaseSchema();
    }

    public function testGmvNoSales(): void
    {
        $periodStart = new \DateTime('2024-01-01');
        $periodEnd = new \DateTime('2024-05-31');

        $gmv = $this->gmvProvider->getGmvForPeriod($periodStart, $periodEnd);

        $this->assertEmpty($gmv);
    }

    public function testGmvNoShippingNoTaxes(): void
    {
        $this->loadFixtures('sales_no_shipping_no_taxes.yaml');

        $periodStart = new \DateTime('2024-01-01');
        $periodEnd = new \DateTime('2024-05-31');

        $gmv = $this->gmvProvider->getGmvForPeriod($periodStart, $periodEnd);

        $this->assertEquals(
            ['USD' => '$1,377.00', 'EUR' => '€2,055.48'],
            $gmv,
        );
    }

    public function testGmvWithShippingAndTaxes(): void
    {
        $this->loadFixtures('sales_with_shipping_and_taxes.yaml');

        $periodStart = new \DateTime('2024-01-01');
        $periodEnd = new \DateTime('2024-05-31');

        $gmv = $this->gmvProvider->getGmvForPeriod($periodStart, $periodEnd);

        $this->assertEquals(
            ['USD' => '$8,494.00', 'EUR' => '€20,666.48'],
            $gmv,
        );
    }

    private function createDatabaseSchema(): void
    {
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }

    private function loadFixtures(string $filename): void
    {
        $container = static::getContainer();

        $loader = $container->get('fidry_alice_data_fixtures.loader.doctrine');
        $loader->load([__DIR__ . '/../DataFixtures/' . $filename], [], []);
    }
}
