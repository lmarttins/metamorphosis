<?php declare(strict_types=1);
namespace Metamorphosis\Config;

use Metamorphosis\Exceptions\ConfigurationException;
use Metamorphosis\TopicHandler\Consumer\Handler;

/**
 * Maps configuration from config file and provides access to them via methods.
 */
class Consumer extends AbstractConfig
{
    /**
     * @var string
     */
    protected $consumerGroupId;

    /**
     * @var int
     */
    protected $consumerPartition;

    /**
     * @var string
     */
    protected $consumerOffsetReset;

    /**
     * @var int
     */
    protected $consumerOffset;

    /**
     * @var Handler
     */
    protected $consumerHandler;

    public function __construct(
        string $topic,
        string $consumerGroupId = null,
        int $partition = null,
        int $offset = null
    ) {
        parent::__construct($topic);
        $this->setConsumerGroup($this->getTopicConfig($topic), $consumerGroupId, $partition, $offset);
    }

    public function getConsumerGroupId(): string
    {
        return $this->consumerGroupId;
    }

    public function getConsumerOffsetReset(): string
    {
        return $this->consumerOffsetReset;
    }

    public function getConsumerOffset(): int
    {
        return $this->consumerOffset;
    }

    public function getConsumerHandler(): Handler
    {
        return $this->consumerHandler;
    }

    public function getConsumerPartition(): ?int
    {
        return $this->consumerPartition;
    }

    private function setConsumerGroup(
        array $topicConfig,
        string $consumerGroupId = null,
        int $partition = null,
        int $offset = null
    ): void {
        if (!$consumerGroupId && count($topicConfig['consumer-groups']) === 1) {
            $consumerGroupId = current(array_keys($topicConfig['consumer-groups']));
        }

        $consumerGroupId = $consumerGroupId ?? 'default';

        $consumerConfig = $topicConfig['consumer-groups'][$consumerGroupId] ?? null;

        if (!$consumerConfig) {
            throw new ConfigurationException("Consumer group '{$consumerGroupId}' not found");
        }

        $this->consumerGroupId = $consumerGroupId;
        $this->consumerPartition = !is_null($partition) ? $partition : ($consumerConfig['partition'] ?? null);
        $this->consumerOffsetReset = $consumerConfig['offset-reset'] ?? 'largest';
        $this->consumerOffset = !is_null($offset) ? $offset : $consumerConfig['offset'];
        $this->consumerHandler = app($consumerConfig['consumer']);

        $this->setMiddlewares($consumerConfig['middlewares'] ?? []);
    }

    protected function setGlobalMiddlewares(): void
    {
        parent::setGlobalMiddlewares();
        $this->setMiddlewares(config('kafka.middlewares.consumer', []));
    }
}