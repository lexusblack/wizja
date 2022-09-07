<?php
namespace common\widgets;

class LanguagePicker extends \lajax\languagepicker\widgets\LanguagePicker
{
    public function init()
    {
        $this->skin = \lajax\languagepicker\widgets\LanguagePicker::SKIN_DROPDOWN;
        $this->size = \lajax\languagepicker\widgets\LanguagePicker::SIZE_SMALL;
        $this->languages = \common\models\Language::getCodesList();
        parent::init();
    }

}