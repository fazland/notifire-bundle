<?php declare(strict_types=1);

namespace Fazland\NotifierBundle\Tests\DependencyInjection;

use Fazland\NotifireBundle\DependencyInjection\Configuration;
use Fazland\SkebbyRestClient\Constant\SendMethods;
use phpmock\prophecy\PHPProphet;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends TestCase
{
    /**
     * @var Processor
     */
    private $processor;

    public function setUp()
    {
        $this->processor = new Processor();
    }

    /**
     * @runInSeparateProcess
     */
    public function testConfigureSmsSkebbyNotPresent()
    {
        $prophet = new PHPProphet();

        $namespace = $prophet->prophesize('Fazland\NotifireBundle\DependencyInjection');
        $namespace->class_exists(SendMethods::class)
            ->shouldBeCalled()
            ->willReturn(false);

        $namespace->reveal();

        $services = [
            'client1' => [
                'provider' => 'twilio',
                'username' => 'foo_twilio',
                'password' => 'bar_twilio',
                'sender' => '+393668887789',
            ],
        ];

        $configuration = $this->getConfigs([
            'sms' => [
                'services' => $services,
            ],
        ]);

        $this->assertEquals([
            'email' => [
                'enabled' => false,
                'auto_configure_swiftmailer' => true,
                'mailers' => [],
            ],
            'sms' => [
                'enabled' => true,
                'services' => [
                    'client1' => [
                        'provider' => 'twilio',
                        'username' => 'foo_twilio',
                        'password' => 'bar_twilio',
                        'sender' => '+393668887789',
                        'twilio_messaging_service_sid' => null,
                        'composite' => [
                            'providers' => [],
                            'strategy' => 'rand',
                        ],
                        'logger_service' => null,
                    ],
                ],
            ],
        ], $configuration);

        $prophet->checkPredictions();
    }

    public function testUnconfigured()
    {
        $configuration = $this->getConfigs([]);

        $this->assertEquals([
            'email' => [
                'enabled' => false,
                'auto_configure_swiftmailer' => true,
                'mailers' => [],
            ],
            'sms' => [
                'enabled' => false,
                'services' => [],
            ],
        ], $configuration);
    }

    public function testConfigureSms()
    {
        $services = [
            'client1' => [
                'provider' => 'twilio',
                'username' => 'foo_twilio',
                'password' => 'bar_twilio',
                'sender' => '+393668887789',
            ],
            'client2' => [
                'provider' => 'skebby',
                'username' => 'foo_skebby',
                'password' => 'bar_skebby',
                'sender' => '+393668887789',
            ],
            'client3' => [
                'provider' => 'twilio',
                'username' => 'foo_twilio',
                'password' => 'bar_twilio',
                'sender' => '+393668887789',
                'twilio_messaging_service_sid' => 'service_foo_bar',
            ],
        ];

        $configuration = $this->getConfigs([
            'sms' => [
                'services' => $services,
            ],
        ]);

        $this->assertEquals([
            'email' => [
                'enabled' => false,
                'auto_configure_swiftmailer' => true,
                'mailers' => [],
            ],
            'sms' => [
                'enabled' => true,
                'services' => [
                    'client1' => [
                        'provider' => 'twilio',
                        'username' => 'foo_twilio',
                        'password' => 'bar_twilio',
                        'sender' => '+393668887789',
                        'twilio_messaging_service_sid' => null,
                        'composite' => [
                            'providers' => [],
                            'strategy' => 'rand',
                        ],
                        'method' => 'send_sms_basic',
                        'logger_service' => null,
                    ],
                    'client2' => [
                        'provider' => 'skebby',
                        'username' => 'foo_skebby',
                        'password' => 'bar_skebby',
                        'sender' => '+393668887789',
                        'twilio_messaging_service_sid' => null,
                        'composite' => [
                            'providers' => [],
                            'strategy' => 'rand',
                        ],
                        'method' => 'send_sms_basic',
                        'logger_service' => null,
                    ],
                    'client3' => [
                        'provider' => 'twilio',
                        'username' => 'foo_twilio',
                        'password' => 'bar_twilio',
                        'sender' => '+393668887789',
                        'twilio_messaging_service_sid' => 'service_foo_bar',
                        'composite' => [
                            'providers' => [],
                            'strategy' => 'rand',
                        ],
                        'method' => 'send_sms_basic',
                        'logger_service' => null,
                    ],
                ],
            ],
        ], $configuration);
    }

    private function getConfigs(array $configArray)
    {
        $configuration = new Configuration();

        return $this->processor->processConfiguration($configuration, [$configArray]);
    }
}
