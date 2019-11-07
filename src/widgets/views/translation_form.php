<?php
use yii\helpers\Url;
use yii\helpers\Html;
use kilyakus\widget\flag\Flag;
use kilyakus\widget\redactor\Redactor;
?>

<?php foreach (Yii::$app->urlManager->languages as $key => $translation){

    if($attribute == 'title'){
        $content = $form->field($model, 'translations['.$translation.']['.$attribute.']')->label(Yii::t('easyii',ucwords($attribute)));
    }else{
        $content = $form->field($model, 'translations['.$translation.']['.$attribute.']')->widget(Redactor::className(),$redactorOptions)->label(Yii::t('easyii',ucwords($attribute)));
    }

    $languages[$attribute][$key] = [
        'label' => Flag::widget(['pluginSupport' => false, 'flag' => $translation, 'options' => ['class' => 'img-circle', 'width' => 22, 'height' => 22]]),
        'content' => $content,
        'active' => $translation == Yii::$app->language
    ];
} ?>

<div class="language-tabs">
    <?= \yii\bootstrap\Tabs::widget([
        'encodeLabels' => false,
        'items' => $languages[$attribute],
    ]) ?>
</div>

<?php 
$css = <<< CSS
.language-tabs {width:100%;position:relative;}
.language-tabs > .nav-tabs {margin:0;border-bottom:1px solid #ebedf2;}
.language-tabs > .nav-tabs > li > a:hover {background-color:#ebedf2;border-color:transparent transparent #ebedf2;}
.language-tabs > .nav-tabs > li.active > a,
.language-tabs > .nav-tabs > li.active > a:hover,
.language-tabs > .nav-tabs > li.active > a:focus {background:#FFF;border-color:#ebedf2;border-bottom-color:transparent;}
.language-tabs .tab-content {margin-bottom:2rem;padding:1rem;background:#FFF;border:1px solid #ebedf2;border-top:0;border-radius:0 0 4px 4px;box-shadow:0px 0px 13px 0px rgba(82, 63, 105, 0.05);}
.language-tabs .tab-content .form-group {margin-bottom:0;}
CSS;
$this->registerCss($css, ["type" => "text/css"], "behavior-translate" );
?>