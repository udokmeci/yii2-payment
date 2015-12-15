<?php
namespace udokmeci\yii2payment\models;

use Yii;
/**
 *
 * @property string $code
 * @property integer $no
 * @property string $symbol
 * @property integer $E
 *
 */
trait CurrencyTrait 
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code', 'no', 'E'], 'required'],
            [['no', 'E'], 'integer'],
            [['symbol'], 'string'],
            [['code'], 'string', 'max' => 5]
        ];
    }

}
