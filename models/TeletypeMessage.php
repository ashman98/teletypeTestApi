<?php

namespace app\models;

use yii\base\Model;

/**
 * TeletypeMessage represents a message from the Teletype API.
 */
class TeletypeMessage extends Model
{
    public $id;
    public $dialogId;
    public $sessionId;
    public $text;
    public $attachments = [];
    public $operator;
    public $client;
    public $status;
    public $type;
    public $channel;
    public $provider;
    public $isItClient;
    public $seen;
    public $createdAt;
    public $sentAt;
    public $isGroupChat;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['id', 'dialogId', 'sessionId', 'text', 'status', 'type', 'provider'], 'required'],
            [['id', 'dialogId', 'sessionId', 'text'], 'string'],
            [['attachments', 'operator', 'client', 'channel', 'createdAt', 'sentAt'], 'safe'],
            [['status', 'type', 'provider'], 'integer'],
            [['isItClient', 'seen', 'isGroupChat'], 'boolean'],
        ];
    }

    /**
     * Load data from an array.
     *
     * @param array $data
     * @param string $formName
     * @return bool
     */
    public function load($data, $formName = null)
    {
        if (isset($data['operator']) && is_array($data['operator'])) {
            $this->operator = new TeletypeOperator();
            $this->operator->load($data['operator'], '');
        }

        if (isset($data['channel']) && is_array($data['channel'])) {
            $this->channel = new TeletypeChannel();
            $this->channel->load($data['channel'], '');
        }

        return parent::load($data, $formName);
    }
}