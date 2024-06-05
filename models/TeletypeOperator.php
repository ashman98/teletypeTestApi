<?php

namespace app\models;

use yii\base\Model;

/**
 * TeletypeOperator represents an operator in a Teletype message.
 */
class TeletypeOperator extends Model
{
    public $id;
    public $name;
    public $avatar;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['id', 'name'], 'required'],
            [['id', 'name', 'avatar'], 'string'],
        ];
    }
}