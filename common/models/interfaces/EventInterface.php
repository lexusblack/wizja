<?php
namespace common\models\interfaces;

interface EventInterface
{
    public static function getClassTypeLabel();
    public function getClassType();
}