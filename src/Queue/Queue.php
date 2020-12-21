<?php

namespace App\Queue;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Queue
{
    protected AMQPStreamConnection $connection;
    
    protected AMQPChannel $channel;

    protected array $config;

    public function __construct()
    {
        $this->config = include(__DIR__.'/../../config/rabitmq.php');
        $this->setupChannel();
        $this->setupQueue();
    }
    
    protected function setupChannel(): void
    {
        $this->connection = new AMQPStreamConnection(
            $this->config['host'], $this->config['port'], $this->config['username'], $this->config['password']
        );
        $this->channel = $this->connection->channel();
    }

    protected function setupQueue(): void
    {
        $this->channel->queue_declare($this->config['queue'], false, true, false, false);
    }

    public function publish(array $messageBody): self
    {
        $message = new AMQPMessage(json_encode($messageBody), [
            'content_type' => 'application/json',
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
        ]);

        $this->channel->basic_publish($message, '', $this->config['queue']);

        return $this;
    }

    public function close(): void
    {
        $this->channel->close();
        $this->connection->close();
    }

    public function __destruct()
    {
        $this->close();
    }
}