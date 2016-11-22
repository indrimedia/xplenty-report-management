<?php
namespace AppBundle\Service;

use AppBundle\Service\AWSService;
use Aws\Sdk;

class SQSService extends AWSService{

    //default queue, used for logging
    const logqueue = 'AsyncLogQueue';

    /**
     * Constructor for the service
     * @param $app
     */
    public function __construct(){
        parent::__construct();
        $this->sqsclient = $this->aws->createSqs();
        $this->app = $app;
    }

    /**
     * Enqueues a message asynchronously in the selected queue
     * @param $payload
     * @param string $queue
     * @return mixed
     */
    public function enqueue($payload, $queue = self::logqueue){
        try {
            // Send the message
            $promise = $this->sqsclient->sendMessageAsync(array(
                'QueueUrl' => $this->getQueueUrl($queue),
                'MessageBody' => json_encode($payload)
            ));
        }catch(Exception $e){
            throw new Exception('Message could not be enqueued: ' . $e->getMessage());
        }
        return $promise->wait();
    }

    /**
     * Gets messages from the queue, up to 10
     * @param $queue
     * @return mixed|null
     * @throws Exception
     */
    public function getMessagesFromQueue($queue){
        try {
            $result = $this->sqsclient->receiveMessage(array(
                'QueueUrl' => $this->getQueueUrl($queue),
                'MaxNumberOfMessages' => 10
            ));
        }catch(Exception $e){
            throw new Exception('Messages could not be fetched from queue: ' . $e->getMessage());
        }
        return $result['Messages'];
    }

    /**
     * Deletes a message from the queue, based on the receiptHandle
     * @param $queue
     * @param $receiptHandle
     * @return bool
     * @throws Exception
     */
    public function deleteMessage($queue, $receiptHandle){
        try {
            $this->sqsclient->deleteMessage(array(
                'QueueUrl' => $this->getQueueUrl($queue),
                'ReceiptHandle' => $receiptHandle
            ));
        }catch(Exception $e){
            throw new Exception('Message could not be deleted: ' . $e->getMessage());
        }
        return true;
    }

    /**
     * Retrieves the queue url based on the name, this is needed for queue operations
     * @param $queueName
     * @return mixed|null
     */
    private function getQueueUrl($queueName){
        // Get the queue URL from the queue name.
        $result = $this->sqsclient->getQueueUrl(array('QueueName' => $queueName));
        return $result->get('QueueUrl');
    }


}