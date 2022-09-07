<?php
namespace common\components\grid;

use yii\grid\DataColumn;

class NotNullDataColumn extends DataColumn
{

    public function init()
    {
        $this->visible = $this->_getIsVistble();
        parent::init();
    }

    protected function _getIsVistble()
    {
        return true;
        /*
        if ($this->visible == false || $this->attribute == null)
        {
            return $this->visible;
        }
        $query = $this->grid->dataProvider->query;
        $targetClass = $query->modelClass;
        $visible = $targetClass::columnHasValue($this->attribute, $query->where);
        return $visible;
        */
    }

}