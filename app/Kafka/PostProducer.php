<?php

namespace App\Kafka;

use RdKafka\Conf;
use RdKafka\Producer;

class PostProducer
{
    private Producer $producer;

    public function __construct()
    {

        $conf = new Conf();
        $conf->set('bootstrap.servers', env('KAFKA_BOOTSTRAP_SERVERS'));
        $this->producer = new Producer($conf);
    }

    public function produce($message): void
    {

        $topic = $this->producer->newTopic('post-changed-event');

        $topic->produce(RD_KAFKA_PARTITION_UA, 0, serialize($message));
        $this->producer->flush(1000);
    }
}
