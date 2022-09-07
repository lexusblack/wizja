<?php
namespace common\helpers;
class Inflector extends \yii\helpers\Inflector
{
	public static function getClassName($class) {
		$path = explode('\\', $class);
		return array_pop($path);
	}
}