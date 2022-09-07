<?php


namespace backend\modules\permission\models;


use Yii;
use yii\helpers\Html;

class BasePermission {

    const MINE = 1;
    const ALL = 2;
    const SUFFIX = [1 => 'Mine', 2 => 'All'];

    public $label;
    public $unique_id;
    public $dbName;

    public $superuser;

    // 0 or 1 or 2
    public $whoseCanSee = 0;
    public $canSee = null;

    public $next = [];


    public function render($paren_id, $superuser=null) {
        if (($superuser)||(!$this->superuser)){
        $this->renderThis($paren_id);
        $this->renderNext($this->unique_id, $superuser);
    }
    }


    private function renderNext($parent_id, $superuser=null) {
        foreach ($this->next as $permissions) {
            if (($superuser)||(!$permissions->superuser)){
                $permissions->render($parent_id, $superuser);
            }
            
        }
    }


    private function renderThis($parent_id) {
        $class = 'branch expanded';
        if (empty($this->next)) {
            $class = 'leaf expanded';
        }

        $inline = null;
        if ($this->whoseCanSee != 0) {
            $checked1 = null;
            $checked2 = null;
            if ($this->whoseCanSee == self::MINE) {
                $checked1 = 'checked';
            }
            if ($this->whoseCanSee == self::ALL) {
                $checked2 = 'checked';
            }

            $inline = "<input type='radio' name='".$this->getPermissionName()."WhoCanSee' value='".self::MINE."' ".$checked1."> ".Yii::t('app', 'Swoje')." 
                       <input type='radio' name='".$this->getPermissionName()."WhoCanSee' value='".self::ALL."' ".$checked2."> ".Yii::t('app', "Wszystkich");
        }

        $cell_content = $this->label . " " . $inline;
        if ($this->canSee !== null) {
            $cell_content = "<label>" . Html::checkbox($this->getPermissionName(), $this->canSee, ['class' => 'permission_checkbox']) . " " . $this->label . "</label> " . $inline;
        }

        $cell = Html::tag("td", $cell_content);

        echo Html::tag("tr", $cell, [
            'class' => $class,
            'data' => [
                'tt-id' => $this->unique_id,
                'tt-parent-id' => $parent_id
            ]
        ]);

    }

    public function load($post) {
        $this->loadThis($post);
        $this->loadNext($post);
    }

    public function loadThis($post) {
        if ($this->canSee !== null) {
            $this->canSee = (isset($post[$this->getPermissionName()]) && $post[$this->getPermissionName()]);
        }
        if ($this->whoseCanSee != 0) {
            $this->whoseCanSee = $post[$this->getPermissionName().'WhoCanSee'];
        }
    }

    public function loadNext($post) {
        foreach ($this->next as $permissions) {
            $permissions->load($post);
        }
    }



    public function save($role) {
        $this->saveThis($role);
        $this->saveNext($role);
    }

    public function saveThis($role) {
        $manager =  \Yii::$app->authManager;
        if ($this->canSee !== null) {
            $permission = $this->getPermission($this->getPermissionName());

            $exists = key_exists($this->getPermissionName(), $manager->getPermissionsByRole($role->name));
            if ($this->canSee == 1 && !$exists) {
                $manager->addChild($role, $permission);
            }
            if ($this->canSee == 0 && $exists) {
                $manager->removeChild($role, $permission);
            }
        }
        if ($this->whoseCanSee != 0) {
            $permissionName = $this->getPermissionName();
            if ($this->whoseCanSee == self::MINE) {
                $permissionName .= self::SUFFIX[$this->whoseCanSee];
            }
            if ($this->whoseCanSee == self::ALL) {
                $permissionName .= self::SUFFIX[$this->whoseCanSee];
            }
            $permission = $this->getPermission($permissionName);
            $manager->removeChild($role, $this->getPermission($this->getPermissionName().self::SUFFIX[self::ALL]));
            $manager->removeChild($role, $this->getPermission($this->getPermissionName().self::SUFFIX[self::MINE]));
            if ($this->canSee) {
                $manager->addChild($role, $permission);
            }
        }
    }
    public function saveNext($role) {
        foreach ($this->next as $perm) {
            if ($this->whoseCanSee != 0 && $perm->whoseCanSee == 0) {
                $perm->whoseCanSee = $this->whoseCanSee;
            }
            $perm->save($role);
        }
    }

    public function getPermission($permissionName) {
        $manager = \Yii::$app->authManager;
        $permission = $manager->getPermission($permissionName);
        if ($permission == null) {
            $permission = $manager->createPermission($permissionName);
            $manager->add($permission);
        }
        return $permission;
    }

    public function getPermissionName() {
        return $this->dbName;
    }
}