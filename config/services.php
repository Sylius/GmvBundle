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

use Sylius\GmvBundle\Console\Command\GmvCommand;
use Sylius\GmvBundle\Parser\DateParser;
use Sylius\GmvBundle\Parser\DateParserInterface;
use Sylius\GmvBundle\Provider\DefaultDateProvider;
use Sylius\GmvBundle\Provider\DefaultDateProviderInterface;
use Sylius\GmvBundle\Provider\GmvProvider;
use Sylius\GmvBundle\Provider\GmvProviderInterface;
use Sylius\GmvBundle\Validator\InputParametersValidator;
use Sylius\GmvBundle\Validator\InputParametersValidatorInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    $services->set('sylius_gmv.parser.date', DateParser::class)
        ->lazy(DateParserInterface::class)
    ;
    $services->alias(DateParserInterface::class, 'sylius_gmv.parser.date');

    $services->set('sylius_gmv.validator.input_parameters', InputParametersValidator::class)
        ->lazy(InputParametersValidatorInterface::class)
        ->args([
            service('sylius_gmv.parser.date'),
        ])
    ;
    $services->alias(InputParametersValidatorInterface::class, 'sylius_gmv.validator.input_parameters');

    $services->set('sylius_gmv.provider.gmv', GmvProvider::class)
        ->lazy(GmvProviderInterface::class)
        ->args([
            service('sylius.repository.order'),
            service('sylius.formatter.money'),
        ])
    ;
    $services->alias(GmvProviderInterface::class, 'sylius_gmv.provider.gmv');

    $services->set('sylius_gmv.provider.default_date', DefaultDateProvider::class)
        ->lazy(DefaultDateProviderInterface::class)
    ;
    $services->alias(DefaultDateProviderInterface::class, 'sylius_gmv.provider.default_date');

    $services->set('sylius_gmv.console.command.gmv', GmvCommand::class)
        ->args([
            service('sylius_gmv.validator.input_parameters'),
            service('sylius_gmv.parser.date'),
            service('sylius_gmv.provider.gmv'),
            service('sylius_gmv.provider.default_date'),
        ])
        ->tag('console.command')
    ;
};
