<?php

namespace app\components\services;

use RuntimeException;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\httpclient\Client;
use yii\httpclient\Exception;
use yii\httpclient\Response;

class TeletypeSendMessageService extends Component
{
    /** @var string  */
    private string $dialogId = '';
    /** @var string  */
    private string $message = '';

    /**
     * @param string $dialogId
     * @param string $message
     * @param array $config
     */
    public function __construct(string $dialogId,string $message, array $config = [])
    {
        $this->dialogId = $dialogId;
        $this->message = $message;

        parent::__construct($config);
    }

    /**
     * @return void
     */
    public function init(): void
    {
        parent::init();

        $this->validate();
        if (empty($this->message)) {
            $this->message = 'pong!';
        }
    }

    /**
     * Sends a message to the specified dialog.
     *
     * @return Response
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function sendMessage(): Response
    {

        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl(Yii::$app->params['teletypeApiUrl'] . 'message/send?token='.Yii::$app->params['teletypeApiKey'])
            ->setHeaders(['Authorization' => 'Bearer ' . Yii::$app->params['teletypeApiKey']])
            ->setData([
                'dialogId' => $this->dialogId,
                'text' => $this->message,
            ])
            ->send();

        return $response;
    }

    /**
     * @return void
     */
    private function validate(): void
    {
        if (empty($this->dialogId)) {
            Yii::info("Dialog Id is missing", 'teletype_errors');
            throw new RuntimeException('Dialog Id is missing');
        }
        if (!isset(Yii::$app->params['teletypeApiKey'])) {
            Yii::info("Teletype api key not found", 'teletype_errors');
            throw new RuntimeException('Dialog Id is missing');
        }
        if (!isset(Yii::$app->params['teletypeApiUrl'])) {
            Yii::info("Teletype api url not found", 'teletype_errors');
            throw new RuntimeException('Teletype api url not found');
        }
    }
}