<?php


namespace backend\modules\permission\models;


use common\models\User;
use Yii;
use yii\web\NotFoundHttpException;

class PermissionTree extends \yii\base\Model {
    public $users;
    public $assignedUsers;
    public $permissions;

    /* @var Role */
    public $role;
    /* @var \yii\rbac\DbManager */
    public $manager;
    /* @var \backend\modules\permission\models\PermissionTreeStructure */
    public $treeStructure;

    public function rules() {
        $rules = [
            [['users', 'role', 'permissions', 'assignedUsers'], 'safe'],
        ];
        return array_merge(parent::rules(), $rules);
    }

    public function init() {
        parent::init();
        $this->manager = \Yii::$app->authManager;
    }

    public function setRole($role) {
        $this->role = $this->manager->getRole($role);
        if ($this->role === null) {
            throw new NotFoundHttpException(Yii::t('app', 'Rola!'));
        }

        $this->treeStructure = new PermissionTreeStructure($this->role);
    }

    public function render() {
        $this->treeStructure->render();
    }

    public function save() {

        $notAssignedIds = explode(',', $this->users);
        $assignedIds = explode(',', $this->assignedUsers);

        foreach ($notAssignedIds as $userId)
        {
            $this->manager->revoke($this->role, $userId);
        }

        $assigned = $this->manager->getUserIdsByRole($this->role->name);
        foreach ($assignedIds as $userId)
        {
            if (in_array($userId, $assigned) == false && $userId != null)
            {
                $this->manager->assign($this->role, $userId);
            }
        }

        $this->treeStructure->load(Yii::$app->request->post());
        $this->treeStructure->save();
    }

    public function getUserItems() {
        $items = [];

        $users = User::getList();
        $ids = $this->manager->getUserIdsByRole($this->role->name);
        $users = array_diff_key($users, array_flip($ids));

        foreach ($users as $id=>$name) {
            $items[$id] = [
                'content' => $name,
            ];
        }
        return $items;
    }

    public function getAssignedItems() {
        $items = [];
        $ids = $this->manager->getUserIdsByRole($this->role->name);
        $users = User::find()
            ->where(['id'=>$ids])
            ->andWhere(['active'=>1])
            ->orderBy(['last_name'=>SORT_ASC, 'first_name'=>SORT_ASC])
            ->all();
        foreach ($users as $user) {
            $items[$user->id] = [
                'content' => $user->getDisplayLabel(),
            ];
        }

        return $items;
    }
}