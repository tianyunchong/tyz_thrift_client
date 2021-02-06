<?php
namespace Test;
define('THRIFT_ROOT', __DIR__.'/thrift_file/');
require_once "./Thrift/ClassLoader/ThriftClassLoader.php";
use Thrift\ClassLoader\ThriftClassLoader;
$loader = new ThriftClassLoader();
$loader->registerNamespace("Thrift", __DIR__);
$loader->registerNamespace("Example", THRIFT_ROOT. 'gen-php');
$loader->register();

use Thrift\Protocol\TBinaryProtocol;
use Thrift\Transport\TSocket;
use Thrift\Transport\TBufferedTransport;
use Thrift\Exception\TException;
use Example\format_dataClient;


try {
    //socket方式连接服务端
    //数据传输格式和数据传输方式与服务端一一对应
    //如果服务端以http方式提供服务，可以使用THttpClient/TCurlClient数据传输方式
    $transport = new TBufferedTransport(new TSocket('localhost', 8080));
    $protocol = new TBinaryProtocol($transport);
    $client = new format_dataClient($protocol);

    $transport->open();

    //同步方式进行交互
    $recv = $client->do_format(new \Example\Data([
    	"text" => "test11",
    ]));
    var_dump($recv);
    $transport->close();
} catch (TException $tx) {
    print 'TException: '.$tx->getMessage()."\n";
}