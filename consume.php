<?php

$start = time();
$conn = [
    'host' => '134.175.138.178',
    'port' => '5672',
    'login' => 'admin',
    'password' => 'admin',
    'vhost' => '/',
];


//创建连接和channel
$conn = new AMQPConnection($conn);
if (!$conn->connect()) {
    die("Cannot connect to the broker!\n");
}
$channel = new AMQPChannel($conn);
$exchangeName = 'ex1';

//创建交换机
$ex = new AMQPExchange($channel);
$ex->setName($exchangeName);

$ex->setType(AMQP_EX_TYPE_DIRECT); //direct类型
$ex->setFlags(AMQP_DURABLE); //持久化
$ex->declareExchange();

//  创建队列
$queueName = 'queue1';
$q = new AMQPQueue($channel);
$q->setName($queueName);
$q->setFlags(AMQP_DURABLE);
$q->declareQueue();

// 用于绑定队列和交换机，跟 send.php 中的一致。
$routingKey = 'key_1';
$q->bind($exchangeName,  $routingKey);

$start = time();
//接收消息
$q->consume(function ($envelope, $queue) use ($start) {
    $msg = $envelope->getBody();
    echo $msg . "\n"; //处理消息
    echo "1万条数据消费时间为:" . time() - $start . "秒\n";
}, AMQP_AUTOACK);

$conn->disconnect();
