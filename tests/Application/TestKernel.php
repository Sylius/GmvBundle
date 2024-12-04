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

namespace Tests\Sylius\GmvBundle\Application;

use BabDev\PagerfantaBundle\BabDevPagerfantaBundle;
use Bazinga\Bundle\HateoasBundle\BazingaHateoasBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle;
use Fidry\AliceDataFixtures\Bridge\Symfony\FidryAliceDataFixturesBundle;
use FOS\RestBundle\FOSRestBundle;
use JMS\SerializerBundle\JMSSerializerBundle;
use Knp\Bundle\GaufretteBundle\KnpGaufretteBundle;
use League\FlysystemBundle\FlysystemBundle;
use Liip\ImagineBundle\LiipImagineBundle;
use Nelmio\Alice\Bridge\Symfony\NelmioAliceBundle;
use Payum\Bundle\PayumBundle\PayumBundle;
use Sonata\BlockBundle\SonataBlockBundle;
use Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle;
use Sylius\Abstraction\StateMachine\SyliusStateMachineAbstractionBundle;
use Sylius\Bundle\AddressingBundle\SyliusAddressingBundle;
use Sylius\Bundle\AttributeBundle\SyliusAttributeBundle;
use Sylius\Bundle\ChannelBundle\SyliusChannelBundle;
use Sylius\Bundle\CoreBundle\SyliusCoreBundle;
use Sylius\Bundle\CurrencyBundle\SyliusCurrencyBundle;
use Sylius\Bundle\CustomerBundle\SyliusCustomerBundle;
use Sylius\Bundle\FixturesBundle\SyliusFixturesBundle;
use Sylius\Bundle\GridBundle\SyliusGridBundle;
use Sylius\Bundle\InventoryBundle\SyliusInventoryBundle;
use Sylius\Bundle\LocaleBundle\SyliusLocaleBundle;
use Sylius\Bundle\MailerBundle\SyliusMailerBundle;
use Sylius\Bundle\MoneyBundle\SyliusMoneyBundle;
use Sylius\Bundle\OrderBundle\SyliusOrderBundle;
use Sylius\Bundle\PaymentBundle\SyliusPaymentBundle;
use Sylius\Bundle\PayumBundle\SyliusPayumBundle;
use Sylius\Bundle\ProductBundle\SyliusProductBundle;
use Sylius\Bundle\PromotionBundle\SyliusPromotionBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Bundle\ReviewBundle\SyliusReviewBundle;
use Sylius\Bundle\ShippingBundle\SyliusShippingBundle;
use Sylius\Bundle\TaxationBundle\SyliusTaxationBundle;
use Sylius\Bundle\TaxonomyBundle\SyliusTaxonomyBundle;
use Sylius\Bundle\ThemeBundle\SyliusThemeBundle;
use Sylius\Bundle\UiBundle\SyliusUiBundle;
use Sylius\Bundle\UserBundle\SyliusUserBundle;
use Sylius\Calendar\SyliusCalendarBundle;
use Sylius\GmvBundle\SyliusGmvBundle;
use SyliusLabs\DoctrineMigrationsExtraBundle\SyliusLabsDoctrineMigrationsExtraBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\WebpackEncoreBundle\WebpackEncoreBundle;
use winzou\Bundle\StateMachineBundle\winzouStateMachineBundle;

final class TestKernel extends Kernel
{
    public function registerBundles(): array
    {
        $bundles = [
            new FrameworkBundle(),
            new SecurityBundle(),
            new TwigBundle(),
            new DoctrineBundle(),
            new FlysystemBundle(),
            new SyliusCalendarBundle(),
            new SyliusStateMachineAbstractionBundle(),
            new SyliusOrderBundle(),
            new SyliusAddressingBundle(),
            new SyliusAttributeBundle(),
            new SyliusChannelBundle(),
            new SyliusCurrencyBundle(),
            new SyliusCustomerBundle(),
            new SyliusFixturesBundle(),
            new SyliusGmvBundle(),
            new SyliusInventoryBundle(),
            new SyliusLocaleBundle(),
            new SyliusMailerBundle(),
            new SyliusMoneyBundle(),
            new SyliusPaymentBundle(),
            new SyliusProductBundle(),
            new SyliusPromotionBundle(),
            new SyliusReviewBundle(),
            new SyliusShippingBundle(),
            new SyliusTaxationBundle(),
            new SyliusTaxonomyBundle(),
            new SyliusThemeBundle(),
            new SyliusUserBundle(),
            new SyliusUiBundle(),
            new SyliusCoreBundle(),
            new SyliusResourceBundle(),
            new SyliusGridBundle(),
            new BazingaHateoasBundle(),
            new JMSSerializerBundle(),
            new KnpGaufretteBundle(),
            new FOSRestBundle(),
            new LiipImagineBundle(),
            new BabDevPagerfantaBundle(),
            new WebpackEncoreBundle(),
            new winzouStateMachineBundle(),
            new DoctrineMigrationsBundle(),
            new SonataBlockBundle(),
            new SyliusLabsDoctrineMigrationsExtraBundle(),
            new PayumBundle(),
            new StofDoctrineExtensionsBundle(),
            new SyliusPayumBundle(),
            new NelmioAliceBundle(),
            new FidryAliceDataFixturesBundle(),
        ];

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/config/config.yaml');
    }
}
