<?php
namespace kilyakus\package\translate\models;

use Yii;
use yii\helpers\HtmlPurifier;
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
			['description', 'filter', 'filter' => function ($value) {
				$value = HtmlPurifier::process($value, [
					'HTML.Allowed' => 'div[class],p[class],br,b,strong,i,u,s,em,a[href|target],ul,li,ol,hr,span[style|class],font[color|size|style|class],h1,h2,h3,h4,h5,h6,sub,sup,blockquote[class],pre[class],img[alt|src|style],iframe[class|frameborder|src|width|height|scrolling],table[class],tr,th,td',
					'CSS.MaxImgLength' => null,
					'CSS.AllowedProperties' => 'color,background-color,border,width,height,font-family,font-size,font-weight,line-height',
					'HTML.SafeIframe' => true,
					'URI.SafeIframeRegexp' => '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/|www\.facebook\.com/)%',
					'Attr.AllowedFrameTargets' => ['_blank']
				]);
				return $this->strip_tags($value);
			}],
			[['lang', 'title'], EscapeValidator::className()],
		];
	}

	protected function strip_tags($value)
	{
		$tags = ['div','p','span','font','b','strong','i'];
		foreach ($tags as $tag)
		{
			$value = str_replace('<' . $tag . '><br /></' . $tag . '>', '', $value);
		}
		foreach ($tags as $tag)
		{
			$value = str_replace('<' . $tag . '></' . $tag . '>', '', $value);
		}
		$value = preg_replace('~(<(.*)[^<>]*>\s*<\/\\2>)~i', '', $value);
		$value = htmlentities($value);
		$value = str_replace('&nbsp;', '', $value);
		$value = str_replace('background-color:transparent;', '', $value);

		$value = htmlspecialchars_decode($value);

		return $value;
	}

	public function attributeLabels()
	{
		return [
			'lang'			=> 'Language',
			'title'			=> Yii::t('easyii', 'Title'),
			'h1'			=> Yii::t('easyii', 'H1 header'),
			'keywords'		=> Yii::t('easyii', 'Keywords'),
			'short'			=> Yii::t('easyii', 'Short'),
			'text'			=> Yii::t('easyii', 'Text'),
			'description'	=> Yii::t('easyii', 'Description'),
		];
	}
}
