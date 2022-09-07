<?php
namespace common\helpers;

class StringHelper
{
    public static function parseText($text, $data) {
        $paresedText = $text;
        if(is_array($data) == false) {
            $data = [$data];
        }
        foreach ($data as $key => $obj) {
            if (!method_exists($obj,'getPlaceholderMap')) {
                continue;
            }
            $map = $obj->getPlaceholderMap();
            $search = [];
            $replace = [];

            foreach ($map as $k=>$v) {
                $search[] = '/\{'.$k.'\}/';
                $replace[] = $v;
            }
            $paresedText = preg_replace($search, $replace, $paresedText);
        }

        return $paresedText;
    }
}