<?php
/*
 * With reference to https://github.com/arnaud-lb/php-rdkafka
 * With reference to https://packagist.org/packages/hauptmedia/rdkafka
 * With reference to https://github.com/hauptmedia/php-rdkafka
 * With reference to https://arnaud-lb.github.io/php-rdkafka/phpdoc/rdkafka.examples.html
 * */
namespace Lib\Support\Kafka;
use Kerisy\Core\Object;

class Kafka extends Object
{
    public $host = '127.0.0.1';
    public $port = 9092;

    private $kafka;
    private $conf; //for consumer
    private $topic;

    CONST TIME_OUT = -1;

    public function init()
    {
        if ( !in_array("rdkafka", get_loaded_extensions()) )
        {
            throw new \Exception("not exist rdkafka extensions error.");
        }
    }
   
    /**
     * [producer 实例化生产者]
     * @return [type] [description]
     */
    public function producer()
    {
        try
        {
            $kafka = new \RdKafka\Producer();
            $kafka->setLogLevel(LOG_DEBUG);
            $kafka->addBrokers( $this->host . ':' . $this->port );
            
            $this->kafka = $kafka;
        } 
        catch (\Exception $e) 
        {
            throw new \Exception($e->getMessage());
        }
    }
	
    /**
     * [producerTopic 生产者topic]
     * @param  [type] $topicName [description]
     * @return [type]            [description]
     */
    public function producerTopic($topicName)
    {
        $this->topic = $this->kafka->newTopic($topicName);
    }

    /**
     * [send 生产者发送消息]
     * @param  [type] $msg       [description]
     * @param  [type] $key       [description]
     * @param  [type] $partition [description]
     * @return [type]            [description]
     */
    public function send( $msg, $key = null, $partition = RD_KAFKA_PARTITION_UA )
    {
        $this->topic->produce($partition, 0, $msg, $key);
    }
	

    /**
     * [consumerGroup 消费者组]
     * @param  [type] $groupName [description]
     * @return [type]            [description]
     */
    public function consumerGroup( $groupName )
    {
        $conf = new \RdKafka\Conf();
		
        $conf->setRebalanceCb(function (\RdKafka\KafkaConsumer $kafka, $err, array $partitions = null){
            switch ($err) 
            {
                case RD_KAFKA_RESP_ERR__ASSIGN_PARTITIONS:
                        $kafka->assign($partitions);
                    break;
                case RD_KAFKA_RESP_ERR__REVOKE_PARTITIONS:
                        $kafka->assign(NULL);
                    break;
                default:
                    throw new \Exception($err);
            }
        });

        $conf->set('group.id', $groupName);
        $conf->set('metadata.broker.list', $this->host . ':' . $this->port);

        $topicConf = new \RdKafka\TopicConf();
        $topicConf->set('auto.offset.reset', 'smallest');
        $conf->setDefaultTopicConf($topicConf);

        $this->conf = $conf;
    }

    /**
     * [consumer 实例化消费者]
     * @return [type] [description]
     */
    public function consumer()
    {
        try
        {
            $this->kafka = new \RdKafka\KafkaConsumer($this->conf);
           
        }
        catch (\Exception $e) 
        {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * [consumeTopic 消费者topic]
     * @param  [type] $topicName [description]
     * @return [type]            [description]
     */
    public function consumeTopic($topicName)
    {
        $this->kafka->subscribe([$topicName]);
    }

    /**
     * [get 消费者获取内容]
     * @return [type] [description]
     */
    public function get()
    {
        $result = [];
        while (true) 
        {
            $message = $this->kafka->consume(self::TIME_OUT);
            $rs = $this->parseData($message);

            if($rs){
                $result[] = $rs;
            }else{
                break;
            }
        }
        return $result;
    }
    
    //数据处理
    private function parseData($msg)
    {
        $result = [];
        if (!$msg) {
            return $result;
        }
        switch ($msg->err) {
            case RD_KAFKA_RESP_ERR_NO_ERROR:
                if ($msg->key) {
                    $result = [$msg->key => $msg->payload];
                } else {
                    $result = $msg->payload;
                }
                break;
            case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                //echo rd_kafka_err2str(RD_KAFKA_RESP_ERR__PARTITION_EOF) . PHP_EOL;
                break;
            case RD_KAFKA_RESP_ERR__TIMED_OUT:
                //echo rd_kafka_err2str(RD_KAFKA_RESP_ERR__TIMED_OUT) . PHP_EOL;
                break;
            default:
                break;
        }
        return $result;
    }
}
