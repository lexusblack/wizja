<?php

namespace common\models;

use common\helpers\ImageHelper;
use Yii;
use \common\models\base\Location as BaseLocation;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "location".
 */
class Location extends BaseLocation
{

    const TYPE_PUBLIC = 0;
    const TYPE_PRIVATE = 1;

    public $existingModels = null;

    public function rules()
    {
        $rules = [
            [['name'], 'required'],
            [['name'], 'checkIfExist'],
        ];
        return array_merge(parent::rules(), $rules);
    }

    public function saveCoordinates()
    {
        if (!$this->longitude)
        {
                $to =  $this->city.', '.$this->address;
                $to = urlencode($to);
                $apiKey= "AIzaSyAPDBOEfgjSaEHEiC8Zx3BpV5lT_cIRiBQ";  
                $data = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=".$to."&key=".$apiKey);
                $data = json_decode($data, true);
                if ($data['status']=="OK")
                {
                    if (isset($data['results'][0]))
                    {
                       $r = $data['results'][0];
                       $this->latitude = $r['geometry']['location']['lat'];
                       $this->longitude = $r['geometry']['location']['lng'];
                       $this->save();
                    }
                }
                //echo var_dump($data);
        }
    }

    public static function getList($term=null)
    {
        $models = static::find()
            ->andFilterWhere([ 'or',
                ['like', 'name', $term],
                ['like', 'city', $term]

            ])->andWhere(['active'=>1])
            ->all();
        $list = [];

        foreach ($models as $model)
        {
            $list[$model->id] = $model->getDisplayLabel();
        }

        return $list;
    }

    public static function getIds($editable=false)
    {
        if ($editable)
            $models= static::find()->editable()->all();
        else
            $models = static::find()->all();
        $list = [];

        foreach ($models as $model)
        {
            $list[] = $model->id;
        }

        return $list;
    }

    public function getDisplayLabel()
    {
        $attributes = [
            $this->name,
            $this->address,
            $this->city,
            $this->country,
        ];
        $attributes = array_filter($attributes);
        return implode(', ', $attributes);
    }

    public function getAssignedAttachements($params = [])
    {
        $params = array_merge(
            Yii::$app->request->queryParams,
            $params
        );
        $query = $this->getLocationAttachments();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }

    public function getAssignedPanoramas()
    {
        $query = $this->getLocationPanoramas();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;      
    }

    public function getAssignedGalleries()
    {
        $query = $this->getLocationPhotos();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;      
    }
    public function getAssignedPlans()
    {
        $query = $this->getLocationPlans();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;      
    }
    public function getGalleryAttachments()
    {
        $models = $this->getLocationAttachments()->andWhere(['type'=>LocationAttachment::TYPE_IMAGE])->all();

        return $models;
    }


    public function getPhotoUrl($thumb = false)
    {
        if ($this->photo == null)
        {
            return null;
        }
        else
        {

            if ($thumb == false)
            {
                if ($this->public==2)
                    $url =  Yii::getAlias('@uploadsAll/location/'.$this->photo);
                else
                    $url =  Yii::getAlias('@uploads/location/'.$this->photo);
            }
            else
            {
                if ($this->public==2)
                    $path =  Yii::getAlias('@uploadroot/location/'.$this->photo);
                else
                    $path =  Yii::getAlias('@uploadrootAll/location/'.$this->photo);

                $url = ImageHelper::getFileThumbnailUrl($path);
            }

            return $url;
        }

    }

    public function getGoogleDistance()
    {
        $distance = 0;
        try
        {
            $origin = [
                Yii::$app->settings->get('companyCity', 'main'),
                Yii::$app->settings->get('companyAddress', 'main')
            ];
            $origin = array_filter($origin);
            $destination = [
                $this->city,
                $this->address,
            ];
            $destination = array_filter($destination);
            $obj = new \dosamigos\google\maps\services\DirectionsClient([
                'params' => [
                    'language' => Yii::$app->language,
                    'origin'=> implode(', ', $origin),
                    'destination' => implode(', ', $destination),
                ]
            ]);
            $data = $obj->lookup();
            //echo var_dump($data);
            $distance = $data->routes[0]->legs[0]->distance->value;
            $distance = ceil($distance/1000);
            return $distance;
        }
        catch (\Exception $e)
        {
            return $distance;
        }

        
    }

    public function beforeSave($insert)
    {
        if (!$this->distance )
        {
            //$this->distance = $this->getGoogleDistance();
        }
        if ($insert)
            $this->owner = \Yii::$app->params['companyID'];
        return parent::beforeSave($insert);
    }

    public function checkIfExist($attribute, $params, $validator)
    {
        if ($this->isNewRecord == true)
        {
            //TODO: Trzeba przy zmianie na wielu 'użytkowników' trzeba przefiltrować prywatne.

            if ($this->type == self::TYPE_PUBLIC)
            {
                $query = static::find()
                    ->andFilterWhere(['like', 'city', $this->city])
                    ->andFilterWhere(['like', 'name', $this->name])
                    ->andFilterWhere([
                        'zip' => $this->zip
                    ])
                    ->andWhere(['like', 'name', $this->name]);

                $this->existingModels = $query->all();
                if (empty($this->existingModels) == false) {
                    $this->addError($attribute, Yii::t('app', 'Podobne miejsce już instnieje'));
                }
            }
        }

    }

    public static function getStarList()
    {   $list = range(0,5);
        $list = array_combine($list, $list);
        return $list;
    }

    public static function getPublicList()
    {
        $list = [0=>Yii::t('app', "Prywatne"), 1=>Yii::t('app', 'Publiczne niezaakceptowane'), 2=>Yii::t('app', "Publiczne")];
        return $list;
    }  

    public static function getCountries()
    {
        $lista_panstw = array(
            "1"=>"Abchazja",
            "2"=>"Afganistan",
            "3"=>"Albania",
            "4"=>"Algieria",
            "5"=>"Andora",
            "6"=>"Angola",
            "7"=>"Antigua i Barbuda",
            "8"=>"Arabia Saudyjska",
            "9"=>"Argentyna",
            "10"=>"Armenia",
            "11"=>"Australia",
            "12"=>"Austria",
            "13"=>"Azerbejdżan",
            "14"=>"Bahamy",
            "15"=>"Bahrajn",
            "16"=>"Bangladesz",
            "17"=>"Barbados",
            "18"=>"Belgia",
            "19"=>"Belize",
            "20"=>"Benin",
            "21"=>"Bhutan",
            "22"=>"Białoruś",
            "23"=>"Birma",
            "24"=>"Boliwia",
            "25"=>"Bośnia i Hercegowina",
            "26"=>"Botswana",
            "27"=>"Brazylia",
            "28"=>"Brunei",
            "29"=>"Bułgaria",
            "30"=>"Burkina Faso",
            "31"=>"Burundi",
            "32"=>"Chile",
            "33"=>"Chiny",
            "34"=>"Chorwacja",
            "35"=>"Cypr",
            "36"=>"Cypr Północny",
            "37"=>"Czad",
            "38"=>"Czarnogóra",
            "39"=>"Czechy",
            "40"=>"Dania",
            "41"=>"Demokratyczna Republika Konga",
            "42"=>"Dominika",
            "43"=>"Dominikana",
            "44"=>"Dżibuti",
            "45"=>"Egipt",
            "46"=>"Ekwador",
            "47"=>"Erytrea",
            "48"=>"Estonia",
            "49"=>"Etiopia",
            "50"=>"Fidżi",
            "51"=>"Filipiny",
            "52"=>"Finlandia",
            "53"=>"Francja",
            "54"=>"Gabon",
            "55"=>"Gambia",
            "56"=>"Ghana",
            "57"=>"Górski Karabach",
            "58"=>"Grecja",
            "59"=>"Grenada",
            "60"=>"Gruzja",
            "61"=>"Gujana",
            "62"=>"Gwatemala",
            "63"=>"Gwinea",
            "64"=>"Gwinea Bissau",
            "65"=>"Gwinea Równikowa",
            "66"=>"Haiti",
            "67"=>"Hiszpania",
            "68"=>"Holandia",
            "69"=>"Honduras",
            "70"=>"Indie",
            "71"=>"Indonezja",
            "72"=>"Irak",
            "73"=>"Iran",
            "74"=>"Irlandia",
            "75"=>"Islandia",
            "76"=>"Izrael",
            "77"=>"Jamajka",
            "78"=>"Japonia",
            "79"=>"Jemen",
            "80"=>"Jordania",
            "81"=>"Kambodża",
            "82"=>"Kamerun",
            "83"=>"Kanada",
            "84"=>"Katar",
            "85"=>"Kazachstan",
            "86"=>"Kenia",
            "87"=>"Kirgistan",
            "88"=>"Kiribati",
            "89"=>"Kolumbia",
            "90"=>"Komory",
            "91"=>"Kongo",
            "92"=>"Korea Południowa",
            "93"=>"Korea Północna",
            "94"=>"Kosowo",
            "95"=>"Kostaryka",
            "96"=>"Kuba",
            "97"=>"Kuwejt",
            "98"=>"Laos",
            "99"=>"Lesotho",
            "100"=>"Liban",
            "101"=>"Liberia",
            "102"=>"Libia",
            "103"=>"Liechtenstein",
            "104"=>"Litwa",
            "105"=>"Luksemburg",
            "106"=>"Łotwa",
            "107"=>"Macedonia",
            "108"=>"Madagaskar",
            "109"=>"Malawi",
            "110"=>"Malediwy",
            "111"=>"Malezja",
            "112"=>"Mali",
            "113"=>"Malta",
            "114"=>"Maroko",
            "115"=>"Mauretania",
            "116"=>"Mauritius",
            "117"=>"Meksyk",
            "118"=>"Mikronezja",
            "119"=>"Mołdawia",
            "120"=>"Monako",
            "121"=>"Mongolia",
            "122"=>"Mozambik",
            "123"=>"Naddniestrze",
            "124"=>"Namibia",
            "125"=>"Nauru",
            "126"=>"Nepal",
            "127"=>"Niemcy",
            "128"=>"Niger",
            "129"=>"Nigeria",
            "130"=>"Nikaragua",
            "131"=>"Norwegia",
            "132"=>"Nowa Zelandia",
            "133"=>"Oman",
            "134"=>"Osetia Południowa",
            "135"=>"Pakistan",
            "136"=>"Panama",
            "137"=>"Papua - Nowa Gwinea",
            "138"=>"Paragwaj",
            "139"=>"Peru",
            "140"=>"Polska",
            "141"=>"Portugalia",
            "142"=>"Republika Południowej Afryki",
            "143"=>"Republika Środkowoafrykańska",
            "144"=>"Republika Zielonego Przylądka",
            "145"=>"Rosja",
            "146"=>"Rumunia",
            "147"=>"Rwanda",
            "148"=>"Saint Kitts i Nevis",
            "149"=>"Saint Lucia",
            "150"=>"Saint Vincent i Grenadyny",
            "151"=>"Salwador",
            "152"=>"Samoa",
            "153"=>"San Marino",
            "154"=>"Senegal",
            "155"=>"Serbia",
            "156"=>"Seszele",
            "157"=>"Sierra Leone",
            "158"=>"Singapur",
            "159"=>"Słowacja",
            "160"=>"Słowenia",
            "161"=>"Somalia",
            "162"=>"Somaliland",
            "163"=>"Sri Lanka",
            "164"=>"Stany Zjednoczone",
            "165"=>"Suazi",
            "166"=>"Sudan",
            "167"=>"Surinam",
            "168"=>"Syria",
            "169"=>"Szwajcaria",
            "170"=>"Szwecja",
            "171"=>"Tadżykistan",
            "172"=>"Tajlandia",
            "173"=>"Tajwan",
            "174"=>"Tanzania",
            "175"=>"Timor Wschodni",
            "176"=>"Togo",
            "177"=>"Tonga",
            "178"=>"Trynidad i Tobago",
            "179"=>"Tunezja",
            "180"=>"Turcja",
            "181"=>"Turkmenistan",
            "182"=>"Tuvalu",
            "183"=>"Uganda",
            "184"=>"Ukraina",
            "185"=>"Urugwaj",
            "186"=>"Uzbekistan",
            "187"=>"Vanuatu",
            "188"=>"Watykan",
            "189"=>"Wenezuela",
            "190"=>"Węgry",
            "191"=>"Wielka Brytania",
            "192"=>"Wietnam",
            "193"=>"Włochy",
            "194"=>"Wybrzeże Kości Słoniowej",
            "195"=>"Wyspy Salomona",
            "196"=>"Wyspy Świętego Tomasza i Książęca",
            "197"=>"Zambia",
            "198"=>"Zimbabwe",
            "199"=>"Zjednoczone Emiraty Arabskie"
            );

            $lista = [];
            foreach ($lista_panstw as $l)
            {
                $country = new Country(['name'=>$l]);
                $country->save();
                $lista[Yii::t('app', $l)] = Yii::t('app', $l);
            }

        return $lista;
    } 
}
