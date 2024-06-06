<?php

namespace app\components;

use Yii;
use yii\base\BaseObject;

class TeletypeLogs extends BaseObject
{
    /**
     * @param $message
     * @return void
     */
    public function logIncomingMessage($message): void
    {
        Yii::info('Incoming message: ' . $message, 'clients');
    }

    /**
     * @param $message
     * @return void
     */
    public function logOutgoingMessage($message): void
    {
        Yii::info('Outgoing message: ' . $message, 'operators');
    }

    /**
     * @param $message
     * @return void
     */
    public function logTeletypeErrors($message): void
    {
        Yii::info('Teletype errors: ' . $message, 'teletype_errors');
    }
}