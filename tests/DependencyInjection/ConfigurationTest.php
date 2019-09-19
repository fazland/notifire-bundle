<?php declare(strict_types=1);

namespace Fazland\NotifierBundle\Tests\DependencyInjection;

use Fazland\NotifireBundle\DependencyInjection\Configuration;
use Fazland\NotifireBundle\Utils\ClassUtils;
use Fazland\SkebbyRestClient\Constant\SendMethods;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends TestCase
{
    /**
     * @var ClassUtils|ObjectProphecy
     */
    private $classUtils;

    /**
     * @var Processor
     */
    private $processor;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->classUtils = $this->prophesize(ClassUtils::class);
        $this->processor = new Processor();
    }

    public function testNotConfigured()
    {
        $this->classUtils->exists(SendMethods::class)->willReturn(false);

        $configuration = $this->getConfigs([]);

        self::assertEquals([
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

        $this->classUtils->exists(SendMethods::class)->willReturn(true);
        $configuration = $this->getConfigs([
            'sms' => [
                'services' => $services,
            ],
        ]);

        self::assertEquals([
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

    private function getConfigs(array $configArray): array
    {
        $configuration = new Configuration($this->classUtils->reveal());

        return $this->processor->processConfiguration($configuration, [$configArray]);
    }
}
