<?php
namespace kilyakus\package\translate\behaviors;

use Yii;
use yii\db\ActiveRecord;
use kilyakus\package\translate\models\TranslateText;

class TranslateBehavior extends \yii\base\Behavior
{
    private $_model;

    private $_translations = [];

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeInsert',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeUpdate',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdate',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
        ];
    }

    public function setTranslations($values)
    {   
        $this->_translations = $values;
    }

    public function getTranslations()
    {
        if($translations = $this->owner->hasMany(TranslateText::className(), ['item_id' => $this->owner->primaryKey()[0]])->where(['class' => get_class($this->owner)])->all())
        {
            foreach ($translations as $translation)
            {
                $_translations[$translation->lang] = $translation;
            }

            return $_translations;
        }

        return false;
    }

    public function beforeInsert()
    {
        self::beforeTranslate();
    }

    public function afterInsert()
    {
        self::afterTranslate();
    }

    public function beforeUpdate()
    {
        self::beforeTranslate();
    }

    public function afterUpdate()
    {
        self::afterTranslate();
    }

    public function beforeTranslate()
    {
        if($this->owner->load(Yii::$app->request->post()))
        {
            if(($post = Yii::$app->request->post((new \ReflectionClass($this->owner))->getShortName())) && isset($post['translations']))
            {
                $current = $post['translations'][Yii::$app->language];

                if($current['title']){$this->owner->title = $current['title'];}
                if($current['h1']){$this->owner->h1 = $current['h1'];}
                if($current['keywords']){$this->owner->keywords = $current['keywords'];}
                if($current['short']){$this->owner->short = $current['short'];}
                if($current['text']){$this->owner->text = $current['text'];}
                if($current['description']){$this->owner->description = $current['description'];}

                foreach ($post['translations'] as $translation)
                {
                    if(empty($current['title']) && !empty($translation['title'])){
                        $this->owner->title = $translation['title'];
                    }

                    if(empty($current['h1']) && !empty($translation['h1'])){
                        $this->owner->h1 = $translation['h1'];
                    }

                    if(empty($current['keywords']) && !empty($translation['keywords'])){
                        $this->owner->keywords = $translation['keywords'];
                    }

                    if(empty($current['short']) && !empty($translation['short'])){
                        $this->owner->short = $translation['short'];
                    }

                    if(empty($current['text']) && !empty($translation['text'])){
                        $this->owner->text = $translation['text'];
                    }

                    if(empty($current['description']) && !empty($translation['description'])){
                        $this->owner->description = $translation['description'];
                    }
                }
            }
        }
    }

    public function afterTranslate()
    {
        if($this->owner->load(Yii::$app->request->post()))
        {
            if($post = Yii::$app->request->post((new \ReflectionClass($this->owner))->getShortName())['translations'])
            {
                $ownerClass = $this->owner;

                foreach ($post as $lang => $translation)
                {
                    if(!$translate = TranslateText::find()->where(['class' => $ownerClass::className(), 'item_id' => $this->owner->primaryKey, 'lang' => $lang])->one()){
                        $translate = new TranslateText();
                    }

                    $translate->load(['TranslateText' => $translation]);
                    $translate->class = $ownerClass::className();
                    $translate->item_id = $this->owner->primaryKey;
                    $translate->lang = $lang;
                    $translate->save();
            
                }

                foreach ($post as $lang => $translation)
                {
                    $translate = TranslateText::find()->where(['class' => $ownerClass::className(), 'item_id' => $this->owner->primaryKey, 'lang' => $lang])->one();
                    
                    if($translate && !$translation['title'] && isset($this->owner->title)){
                        $translate->title = $this->owner->title;
                        $translate->update();
                    }
                }
            }
        }
    }

    public function afterDelete()
    {
        TranslateText::deleteAll(['class' => get_class($this->owner), 'item_id' => $this->owner->primaryKey]);
    }

    public function getTranslate()
    {
        $translate = $this->owner->hasOne(TranslateText::className(), ['item_id' => $this->owner->primaryKey()[0]])->where(['class' => get_class($this->owner), 'lang' => Yii::$app->language]);

        if(!$translate->one()){
            $translate = $this->owner;
        }

        return $translate;
    }

    public function getTranslateText()
    {
        if(!$this->_model)
        {
            $this->_model = $this->owner->translate;
            if(!$this->_model){
                $this->_model = new TranslateText([
                    'class' => get_class($this->owner),
                    'item_id' => $this->owner->primaryKey,
                    'lang' => Yii::$app->language
                ]);
            }
        }

        return $this->_model;
    }
}