<?php
namespace common\components;

use Yii;

class LanguageComponent extends \lajax\languagepicker\Component
{
    public $cookieName = 'applicationLanguage';

    public function initLanguage()
    {
        if (isset($_GET['language-picker-language'])) {
            if ($this->_isValidLanguage($_GET['language-picker-language'])) {
                return $this->saveLanguage($_GET['language-picker-language']);
            } else if (!Yii::$app->request->isAjax) {
                return $this->_redirect();
            }
        } else if (Yii::$app->request->cookies->has($this->cookieName)) {
            if ($this->_isValidLanguage(Yii::$app->request->cookies->getValue($this->cookieName))) {
                Yii::$app->language = Yii::$app->request->cookies->getValue($this->cookieName);
                return;
            } else {
                Yii::$app->response->cookies->remove($this->cookieName);
            }
        }


        $this->detectLanguage();
    }

    public function detectLanguage()
    {
        $acceptableLanguages = $this->languages;
        foreach ($acceptableLanguages as $language) {
            if ($this->_isValidLanguage($language)) {
                Yii::$app->language = $language;
                $this->saveLanguageIntoCookie($language);
                return;
            }
        }

        foreach ($acceptableLanguages as $language) {
            $pattern = preg_quote(substr($language, 0, 2), '/');
            foreach ($this->languages as $key => $value) {
                if (preg_match('/^' . $pattern . '/', $value) || preg_match('/^' . $pattern . '/', $key)) {
                    Yii::$app->language = $this->_isValidLanguage($key) ? $key : $value;
                    $this->saveLanguageIntoCookie(Yii::$app->language);
                    return;
                }
            }
        }
    }

    protected function _isValidLanguage($language)
    {
        return is_string($language) && (isset($this->languages[$language]) || in_array($language, $this->languages));
    }
}