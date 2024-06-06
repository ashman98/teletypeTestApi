<?php

namespace app\controllers;

use app\components\TeletypeLogs;
use app\components\services\TeletypeSendMessageService;
use app\models\TeletypeMessage;
use Yii;
use yii\base\InvalidConfigException;
use yii\httpclient\Exception;
use yii\web\Controller;
use yii\web\Response;

class SiteController extends Controller
{
    public $enableCsrfValidation = false;

    /**
     * @return Response
     */
    public function actionWebhook(): Response
    {
        $teletypeLogger = new TeletypeLogs();

        $request = Yii::$app->request;

        $body = $request->getBodyParam('payload');
        if (empty($body)) {
            $teletypeLogger->logTeletypeErrors("Body not found");
            return $this->asJson(['status' =>  'Body not found']);
        }

        $data = json_decode($body, true);
        $messageData = $data['message'] ?? [];
        $message = new TeletypeMessage();
        $message->load($messageData, '');

        if (!$message->validate()) {
            $teletypeLogger->logTeletypeErrors("Invalid message data  ".json_encode($messageData));
            return $this->asJson(['status' => 'message validate']);
        }

        if ($message->isItClient) {
            $teletypeLogger->logIncomingMessage($message->text);

            if ($message->text ==='ping?') {
                try {
                    $teletypeService = new TeletypeSendMessageService($message->dialogId,'pong!');
                    $response = $teletypeService->sendMessage();

                    if ($response->isOk){
                        $teletypeLogger->logOutgoingMessage('pong! send');
                        return $this->asJson(['status' => 'send']);
                    }

                    $teletypeLogger->logTeletypeErrors($response->getStatusCode());
                } catch (InvalidConfigException|Exception $e) {
                    $teletypeLogger->logTeletypeErrors($e->getMessage());
                    return $this->asJson(['status' => 'error', 'message' => $e->getMessage()]);
                }
            }
        } else {
            $teletypeLogger->logOutgoingMessage($message->text);
        }

        return $this->asJson(['status' => 'ok']);
    }
}
