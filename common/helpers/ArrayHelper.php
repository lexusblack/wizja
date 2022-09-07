<?php
namespace common\helpers;

use yii\helpers\ArrayHelper as BaseArrayHelper;

class ArrayHelper extends BaseArrayHelper
{
    public static function shuffleAssoc($list) {
        if (!is_array($list)) return $list;

        $keys = array_keys($list);
        shuffle($keys);
        $random = array();
        foreach ($keys as $key) {
            $random[$key] = $list[$key];
        }
        return $random;
    }

    public static function sortDates($list, $filter = true)
    {
        if ($filter == true)
        {
            $list = array_filter($list);
        }

        usort($list, function($a, $b) {
            $dateTimestamp1 = strtotime($a);
            $dateTimestamp2 = strtotime($b);

            return $dateTimestamp1 < $dateTimestamp2 ? -1: 1;
        });

        return $list;
    }

    public static function setKey($array, $key, $default = 0)
    {
        if (isset($array[$key]) == false)
        {
            $array[$key] = $default;
        }
        return $array;
    }

    public static function sumArrays($new, $old)
    {
        foreach ($new as $key => $val)
        {
            $value = static::getValue($old, $key, 0);
            $value += $val;
            $old[$key] = $value;
        }
        return $old;
    }

    public static function cleanData($data)
    {
	    $data = array_unique(array_filter($data));
	    return $data;
    }
}