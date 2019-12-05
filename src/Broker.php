<?php
namespace Metamorphosis;

use Metamorphosis\Authentication\Authentication;
use Metamorphosis\Authentication\Factory;
use RdKafka\Conf;

class Broker
{
    /**
     * @var string
     */
    protected $connections;

    /**
     * @var Authentication
     */
    protected $authentication;

    /**
     * @var string
     */
    private $schemaUri;

    public function __construct($connections, array $authentication = null, string $schemaUri = null)
    {
        $this->setConnections($connections);
        $this->setAuthentication($authentication);
        $this->schemaUri = $schemaUri;
    }

    public function authenticate(Conf $conf): void
    {
        $this->authentication->authenticate($conf);
    }

    public function getConnections(): string
    {
        return $this->connections;
    }

    public function getAuthentication(): Authentication
    {
        return $this->authentication;
    }

    public function getSchemaUri(): ?string
    {
        return $this->schemaUri;
    }

    protected function setConnections($connections): void
    {
        if (is_array($connections)) {
            $this->connections = implode(',', $connections);

            return;
        }

        $this->connections = $connections;
    }

    protected function setAuthentication($authConfig = null): void
    {
        $this->authentication = Factory::make($authConfig);
    }
}
