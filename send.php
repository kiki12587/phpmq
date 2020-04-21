<?php

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

// 用来绑定交换机和队列
$routingKey = 'key_1';

$ex = new AMQPExchange($channel);
//  交换机名称
$exchangeName = 'ex1';
$ex->setName($exchangeName);

// 设置交换机类型
$ex->setType(AMQP_EX_TYPE_DIRECT);
// 设置交换机是否持久化消息
$ex->setFlags(AMQP_DURABLE);
$ex->declareExchange();

$starttime = time();
for ($i = 0; $i < 10000; $i++) {
    $ex->publish(date('H:i:s') . "用户" . $i . "注册", $routingKey);
    echo "Send Message:" . $i . "\n";
}
echo $endtime = time() - $starttime;
