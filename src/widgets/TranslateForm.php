<?php
namespace kilyakus\package\translate\widgets;

use Yii;
use yii\base\Widget;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kilyakus\package\translate\models\TranslateText;

class TranslateForm extends Widget
{
    public $form;

    public $model;

    public $attribute;

    public function init()
    {
        parent::init();

        if (empty($this->model)) {
            throw new InvalidConfigException('Required `model` param isn\'t set.');
        }

        if (empty($this->attribute)) {
            throw new InvalidConfigException('Required `attribute` param isn\'t set.');
        }
    }

    public function run()
    {
        $translations = TranslateText::find()->where(['class' => $this->model::className(), 'item_id' => $this->model->primaryKey])->asArray()->all();
        $translations = ArrayHelper::index($translations, 'lang');

        if(get_class($this->model->translateText) == get_class(new TranslateText())){
            $model = $this->model->translateText;
        }else{
            $model = new TranslateText();
        }

        $model->translations = $translations;

        if(empty($model->translations)){

            foreach (Yii::$app->urlManager->languages as $language) {

                $fields = [];

                if(isset($this->model->title)){
                    $fields['title'] = $this->model->title;
                }

                if(isset($this->model->short)){
                    $fields['short'] = $this->model->short;
                }

                if(isset($this->model->text)){
                    $fields['text'] = $this->model->text;
                }

                if(isset($this->model->description)){
                    $fields['description'] = $this->model->description;
                }

                $model->translations[$language] = $fields;
            }
        }

        echo $this->render('translation_form', [
            'form' => $this->form,
            'model' => $model,
            'attribute' => $this->attribute,

        ]);
    }

}