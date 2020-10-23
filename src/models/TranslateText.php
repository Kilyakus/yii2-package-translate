<?php
namespace kilyakus\package\translate\models;

use Yii;
use kilyakus\validator\escape\EscapeValidator;

class TranslateText extends \kilyakus\modules\components\ActiveRecord
{
    public static function tableName()
    {
        return 'translatetext';
    }

    public function rules()
    {
        return [
            [['lang'], 'required'],
            [['lang', 'title', 'h1', 'keywords', 'short', 'text', 'description'], 'trim'],
            [['lang', 'title', 'h1', 'keywords'], 'string', 'max' => 255],
            [['short', 'text', 'description'], 'safe'],
            [['lang', 'title'], EscapeValidator::className()],
        ];
    }

    public function attributeLabels()
    {
        return [
            'lang' => 'Language',
            'title' => Yii::t('easyii', 'Title'),
            'h1' => Yii::t('easyii', 'H1 header'),
            'keywords' => Yii::t('easyii', 'Keywords'),
            'short' => Yii::t('easyii', 'Short'),
            'text' => Yii::t('easyii', 'Text'),
            'description' => Yii::t('easyii', 'Description'),
        ];
    }
}