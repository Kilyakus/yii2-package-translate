<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Tabs;
use kilyakus\widget\flag\Flag;
use kilyakus\widget\redactor\Redactor;
use kilyakus\package\taggable\widgets\TagsInput;
?>

<?php foreach (Yii::$app->urlManager->languages as $key => $language){

    $value = !empty($model->translations[$language][$attribute]) ? $model->translations[$language][$attribute] : $model->{$attribute};

    if($attribute == 'title' || $attribute == 'h1')
    {
        // $content = $form->field($model, 'translations['.$language.']['.$attribute.']')->label(Yii::t('easyii',ucwords($attribute)));
        $content = Html::activeLabel($model, $attribute);
        $content .= Html::activeTextInput($model, 'translations['.$language.']['.$attribute.']', ['class' => 'form-control', 'value' => $value]);
    }elseif($attribute == 'keywords')
    {
        $content = Html::activeLabel($model, $attribute);
        $content .= TagsInput::widget(['model' => $model, 'attribute' => 'translations['.$language.']['.$attribute.']', 'options' => ['value' => $value, 'class' => 'form-control']]);
    }else
    {
        // $content = $form->field($model, 'translations['.$language.']['.$attribute.']')->widget(Redactor::className(),$redactorOptions)->label(Yii::t('easyii',ucwords($attribute)));
        $redactorOptions['model'] = $model;
        $redactorOptions['attribute'] = 'translations['.$language.']['.$attribute.']';
        $redactorOptions['options']['value'] = $value;
        $content = Html::activeLabel($model, Yii::t('easyii',ucwords($attribute)));
        $content .= Redactor::widget($redactorOptions);
    }

    $languages[$attribute][$language] = [
        'label' => Flag::widget(['pluginSupport' => false, 'flag' => $language, 'options' => ['class' => 'img-circle', 'width' => 22, 'height' => 22]]),
        'content' => $content,
        'active' => $language == Yii::$app->language
    ];
} ?>

<?= Html::tag('div', Tabs::widget(['encodeLabels' => false, 'items' => $languages[$attribute]]), ['class' => 'language-tabs']); ?>

<?php 
$css = <<< CSS
.language-tabs {width:100%;position:relative;}
.language-tabs > .nav-tabs {margin:0;border-bottom:1px solid #ebedf2;}
.language-tabs > .nav-tabs > li > a:hover {background-color:#ebedf2;border-color:transparent transparent #ebedf2;}
.language-tabs > .nav-tabs > li.active > a,
.language-tabs > .nav-tabs > li.active > a:hover,
.language-tabs > .nav-tabs > li.active > a:focus {background:#FFF;border-color:#ebedf2;border-bottom-color:transparent;}
.language-tabs > .tab-content {margin-bottom:2rem;padding:1rem;background:#FFF;border:1px solid #ebedf2;border-top:0;border-radius:0 0 4px 4px;box-shadow:0px 0px 13px 0px rgba(82, 63, 105, 0.05);}
.language-tabs > .tab-content .form-group,
.language-tabs > .tab-content .form-group .note-editor {margin-bottom:0;}
CSS;
$this->registerCss($css, ["type" => "text/css"], "behavior-translate" );
?>