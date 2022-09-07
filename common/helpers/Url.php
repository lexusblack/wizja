<?php
namespace common\helpers;
class Url extends \yii\helpers\Url
{

    /**
     * Jeśli nie zdefiniowany 'poprzeni', używamy 'default'.
     *
     * @param null $name
     * @param null $default
     * @return null|string
     */
    public static function toPrevious($name=null, $default=null)
    {
        $url = parent::previous($name);

        if ($url===null && $default!==null)
        {
            $url = Url::to($default);
        }

        return $url;
    }

}