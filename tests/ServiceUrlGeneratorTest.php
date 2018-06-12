<?php
namespace go1\UtilUrl;

use PHPUnit\Framework\TestCase;

class ServiceUrlGeneratorTest extends TestCase
{
    private $backedUpEnv = [];

    private $envKeys = ['ENV', 'SERVICE_URL_PATTERN', 'GATEWAY_URL'];

    protected function setUp()
    {
        foreach ($this->envKeys as $name) {
            $this->backedUpEnv[$name] = getenv($name);
        }
    }

    protected function tearDown()
    {
        foreach ($this->backedUpEnv as $name => $val) {
            $putenvValue = $name;
            if ($val !== false) {
                $putenvValue .= '='.$val;
            }
            putenv($putenvValue);
        }
    }

    public function testGetInternalUrlDefaults()
    {
        putenv('ENV');
        putenv('SERVICE_URL_PATTERN');

        $testSubject = new ServiceUrlGenerator();

        $url = $testSubject->getInternalUrl('fooService');
        $this->assertEquals('http://fooService.production.go1.service', $url);
    }

    public function testGetInternalUrlEnvironment()
    {
        putenv('ENV=fancy');
        putenv('SERVICE_URL_PATTERN=ftp://SERVICE:ENVIRONMENT@localhost');

        $testSubject = new ServiceUrlGenerator();

        $url = $testSubject->getInternalUrl('fooService');
        $this->assertEquals('ftp://fooService:fancy@localhost', $url);
    }

    public function testGetInternalUrlParametrized()
    {
        $testSubject = new ServiceUrlGenerator('helloEnv', 'custom-SERVICE-pattern-ENVIRONMENT');

        $url = $testSubject->getInternalUrl('fooService');
        $this->assertEquals('custom-fooService-pattern-helloEnv', $url);
    }

    public function testGetInternalUrlRulesService()
    {
        $testSubject = new ServiceUrlGenerator('notproduction', 'SERVICE.ENVIRONMENT.local');

        $url = $testSubject->getInternalUrl('rules');
        $this->assertEquals('rules.production.local', $url);
    }

    public function testStaticGetInternalUrls()
    {
        $result = ServiceUrlGenerator::getInternalUrls(['foo', 'bar'], 'qa', 'SERVICE.ENVIRONMENT.local');
        $this->assertEquals([
            'foo_url' => 'foo.qa.local',
            'bar_url' => 'bar.qa.local',
        ], $result);
    }

    public function testGetPublicGateWayUrl()
    {
        $testSubject = new ServiceUrlGenerator('dev', null, 'http://foo.example.com');
        $this->assertEquals('http://foo.example.com', $testSubject->getPublicGatewayUrl());

        $testSubject = new ServiceUrlGenerator('staging', 'dummy');
        $this->assertEquals('https://api-staging.go1.co', $testSubject->getPublicGatewayUrl());

        $testSubject = new ServiceUrlGenerator('production', 'dummy');
        $this->assertEquals('https://api.go1.co', $testSubject->getPublicGatewayUrl());
    }
}
