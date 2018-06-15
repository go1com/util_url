<?php
namespace go1\UtilUrl;

class ServiceUrlGenerator
{
    /**
     * @var string
     */
    private $urlPattern = '';

    /**
     * @var string
     */
    private $environment = '';

    /**
     * @var string
     */
    private $gatewayUrl = '';

    private const DEFAULT_PATTERN = 'http://SERVICE.ENVIRONMENT.go1.service';

    private const DEFAULT_ENV = 'production';

    /**
     * MicroserviceUrlGenerator constructor.
     * @param string $environment
     * @param string $urlPattern
     * @param string $gatewayUrl
     */
    public function __construct($environment = null, $urlPattern = null, $gatewayUrl = null)
    {
        $this->environment = ((string) $environment) ?: (getenv('ENV') ?: self::DEFAULT_ENV);
        $this->urlPattern = ((string) $urlPattern) ?: (getenv('SERVICE_URL_PATTERN') ?: self::DEFAULT_PATTERN);
        $this->gatewayUrl = ((string) $gatewayUrl) ?: (getenv('GATEWAY_URL') ?: '');
    }

    public function getInternalUrl(string $serviceName) : string
    {
        $environment = $this->getEnvironmentForService($serviceName);
        return str_replace(['SERVICE', 'ENVIRONMENT'], [$serviceName, $environment], $this->urlPattern);
    }

    public function getPublicGatewayUrl() : string
    {
        if ($this->gatewayUrl !== '') {
            return $this->gatewayUrl;
        }

        $suffix = 'production' === $this->environment ? '' : "-{$this->environment}";

        return "https://api{$suffix}.go1.co";
    }

    /**
     * @param string[] $serviceNames
     * @param string $environment
     * @param string $urlPattern
     * @return string[]
     */
    public static function getInternalUrls(array $serviceNames, $environment = null, $urlPattern = null) : array
    {
        $urlGenerator = new static($environment, $urlPattern);
        $retval = [];

        foreach ($serviceNames as $serviceName) {
            $retval["{$serviceName}_url"] = $urlGenerator->getInternalUrl($serviceName);
        }

        return $retval;
    }

    /**
     * There are some services that don't have staging instance yet.
     * @param string $serviceName
     * @return string
     */
    private function getEnvironmentForService(string $serviceName) : string
    {
        return $serviceName === 'rules' ? 'production' : $this->environment;
    }
}
