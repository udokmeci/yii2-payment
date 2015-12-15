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
class Currency extends \yii\db\ActiveRecord
{
    use CurrencyTrait;

    public static function tableName()
    {
        return 'currency';
    }
}
