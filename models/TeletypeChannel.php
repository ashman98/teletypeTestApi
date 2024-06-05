<?php

namespace app\models;

use yii\base\Model;

/**
 * TeletypeChannel represents a channel in a Teletype message.
 */
class TeletypeChannel extends Model
{
    public $id;
    public $name;
    public $type;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['id', 'name', 'type'], 'required'],
            [['id', 'name', 'type'], 'string'],
        ];
    }
}