<?php

declare(strict_types=1);

use Behat\Config\Config;
use Behat\Config\Extension;
use Behat\Config\Profile;
use Behat\Config\TesterOptions;
use Behat\MinkExtension\ServiceContainer\MinkExtension;
use FriendsOfBehat\SuiteSettingsExtension\ServiceContainer\SuiteSettingsExtension;
use FriendsOfBehat\SymfonyExtension\ServiceContainer\SymfonyExtension;
use FriendsOfBehat\VariadicExtension\ServiceContainer\VariadicExtension;
use Robertfausk\Behat\PantherExtension\ServiceContainer\PantherExtension;
use Tests\MangoSylius\ExtendedChannelsPlugin\Kernel;

return (new Config())
    ->import('tests/Behat/Resources/suites.php')
    ->withProfile(
        (new Profile('default'))
            ->withTesterOptions((new TesterOptions())->withErrorReporting(\E_ALL ^ \E_DEPRECATED))
            ->withExtension(new Extension(PantherExtension::class))
            ->withExtension(new Extension(MinkExtension::class, [
                'base_url' => 'http://127.0.0.1:9080', // same port as uses Panther
                'default_session' => 'symfony',
                'javascript_session' => 'panther',
                'sessions' => [
                    'symfony' => [
                        'symfony' => null,
                    ],
                    'panther' => [
                        'panther' => [
                            'manager_options' => [
                                'connection_timeout_in_ms' => 5000,
                                'request_timeout_in_ms' => 120000,
                                'chromedriver_binary' => '/usr/bin/chromedriver',
                                'chromedriver_arguments' => [
                                    '--log-path=tests/Application/var/log/chromedriver.log',
                                    '--verbose',
                                ],
                                'capabilities' => [
                                    'acceptSslCerts' => true,
                                    'acceptInsecureCerts' => true,
                                    'unexpectedAlertBehaviour' => 'accept',
                                ],
                            ],
                            'options' => [
                                'webServerDir' => '%paths.base%/tests/Application/public',
                                'env' => [
                                    'APP_ENV' => 'test',
                                ],
                                'browser_arguments' => [
                                    '--window-size=1200,1000',
                                    '--headless',
                                    '--no-sandbox',
                                    '--disable-dev-shm-usage',
                                    '--disable-gpu',
                                    '--disable-infobars',
                                    '--disable-features=TranslateUI',
                                    '--disable-translate',
                                    '--disable-popup-blocking',
                                    '--disable-blink-features=AutomationControlled',
                                    '--disable-component-extensions-with-background-pages',
                                    '--disable-background-networking',
                                    '--disable-dev-tools',
                                    '--disable-extensions',
                                    '--disable-password-manager-leak-detection',
                                ],
                            ],
                        ],
                    ],
                ],
                'show_auto' => false, // do not automatically open browser on error
            ]))
            ->withExtension(new Extension(SymfonyExtension::class, [
                'bootstrap' => 'tests/Application/config/bootstrap.php',
                'kernel' => [
                    'class' => Kernel::class,
                    'path' => 'tests/Application/src/Kernel.php',
                ],
            ]))
            ->withExtension(new Extension(VariadicExtension::class))
            ->withExtension(new Extension(SuiteSettingsExtension::class, [
                'paths' => [
                    'features',
                ],
            ])),
    );
