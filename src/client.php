<?php
namespace Gc\Ext\Thrift;
require_once dirname(__DIR__)."/Thrift/ClassLoader/ThriftClassLoader.php";
use Thrift\ClassLoader\ThriftClassLoader;
use Exception;

class Client {
	public $host;
	public $port;
	public $topic;
	public $loader;
	public $transport;
	public $protocol;
	public $client;
	private $errMsg;

	public function __construct() {
		$this->loader = new ThriftClassLoader();
	}


	public function connect($host, $port, string $topic) {
		$this->host  = $host;
		$this->port  = $port;
		$this->topic = $topic;
		if (!$this->checkTopic($topic)) {
			throw new Exception(sprintf("topic:%s不存在", $topic));
		}
		$this->transport = new \Thrift\Transport\TBufferedTransport(new \Thrift\Transport\TSocket($this->host, $this->port));
		$this->protocol  = new \Thrift\Protocol\TBinaryProtocol($this->transport);
	}

	public function selectService($serviceName) 
	{
		if (!file_exists(dirname(__DIR__)."/thrift_file/gen-php/".ucfirst($this->topic)."/".$serviceName."Client.php")) {
			throw new Exception(sprintf("topic:%s 服务:%s不存在", $this->topic, $serviceName));
		}
		$this->loader->registerNamespace(ucfirst($this->topic), dirname(__DIR__)."/thrift_file/gen-php/");
		$this->loader->register();
		$clientName = sprintf("%s\%sClient", ucfirst($this->topic), $serviceName);
		$this->client = new $clientName($this->protocol);
		$this->transport->open();
	}

	public function recv($method, ...$args) {
		//同步方式进行交互
		try {
			$recv = $this->client->{$method}(...$args);	
		} catch(Exception $e) {
			$this->errMsg = $e->getMessage();
			return "";
		}
		$this->errMsg = "";
	    return $recv;
	}

	public function close() {
		$this->transport->close();
	}

	public function error() {
		return $this->errMsg;
	}

	private function checkTopic($topic) 
	{
		$path = dirname(__DIR__)."/thrift_file/gen-php/".ucfirst($topic);
		return is_dir($path) ? true : false;
	}


}