<?php

namespace app\controllers;

use app\components\TeletypeLogs;
use app\components\TeletypeSendMessageService;
use app\models\TeletypeMessage;
use Yii;
use yii\base\InvalidConfigException;
use yii\filters\AccessControl;
use yii\httpclient\Exception;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
//    /**
//     * {@inheritdoc}
//     */
//    public function behaviors()
//    {
//        return [
//            'access' => [
//                'class' => AccessControl::class,
//                'only' => ['logout'],
//                'rules' => [
//                    [
//                        'actions' => ['logout'],
//                        'allow' => true,
//                        'roles' => ['@'],
//                    ],
//                ],
//            ],
//            'verbs' => [
//                'class' => VerbFilter::class,
//                'actions' => [
//                    'logout' => ['post'],
//                ],
//            ],
//        ];
//    }

    /**
     * {@inheritdoc}
     */
    public function actions(): array
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $e = new TeletypeLogs();
        $e->logTeletypeErrors('ss');
        return 'Hi hello';
    }

    public $enableCsrfValidation = false;

    /**
     * @return Response
     */
    public function actionWebhook(): Response
    {
        $teletypeLogger = new TeletypeLogs();

        $request = Yii::$app->request;
        $body = $request->getRawBody();

        if (!$body) {
            $teletypeLogger->logTeletypeErrors("Invalid data  ".json_encode($body));
            return $this->asJson(['status' =>  'body not found']);
        }

        $data = json_decode($body, true);
        $data = $data['payload'];

        $messageData = $data['message'] ?? [];
//        return $this->asJson(['status' => 'message validate']);
        $message = new TeletypeMessage();
        $message->load($messageData, '');

        if (!$message->validate()) {
            $teletypeLogger->logTeletypeErrors("Invalid message data  ".json_encode($messageData));
            Yii::info("Invalid message data  ".json_encode($messageData), 'custom');
            return $this->asJson(['status' => 'message validate']);
        }

        if ($message->isItClient) {
            $teletypeLogger->logIncomingMessage($message->text);
            if (str_contains($message->text, 'ping?')) {
                try {
                    $teletypeService = new TeletypeSendMessageService($message->dialogId,'pong!');
                    $response = $teletypeService->sendMessage();
                    if ($response->isOk){
                        $teletypeLogger->logOutgoingMessage('pong!');
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
