<?php

namespace macfly\user\client\models;

use Yii;
use yii\helpers\ArrayHelper;

use macfly\user\client\Module;

class RbacManager implements \yii\rbac\ManagerInterface
{
    public function add ($object)
    {
        return self::write('add', [$object]);
    }

    public function addChild($parent, $child)
    {
        return self::write('addChild', [$parent, $child]);
    }

    public function assign($role, $userId)
    {
        return self::write('assign', [$role, $userId]);
    }

    public function canAddChild($parent, $child)
    {
        return self::read('canAddChild', [$parent, $child]);
    }

    public function checkAccess($userId, $permissionName, $params = [])
    {
        $permissionName =	sprintf("%s.%s", Yii::$app->name, $permissionName);
        return self::read('checkAccess', [$userId, $permissionName, $params]);
    }

    public function createPermission($name)
    {
        $name =	sprintf("%s.%s", Yii::$app->name, $name);
        return self::write('createPermission', [$name]);
    }

    public function createRole ($name)
    {
        $name =	sprintf("%s.%s", Yii::$app->name, $name);
        return self::write('createRole', [$name]);
    }

    public function getAssignment($roleName, $userId)
    {
        $roleName =	sprintf("%s.%s", Yii::$app->name, $roleName);
        return self::read('getAssignment', [$roleName, $userId]);
    }

    public function getAssignments($userId)
    {
        return self::read('getAssignments', [$userId]);
    }

    public function getChildRoles($roleName)
    {
        $roleName =	sprintf("%s.%s", Yii::$app->name, $roleName);
        return self::read('getChildRoles', [$roleName]);
    }

    public function getChildren($name)
    {
        $name =	sprintf("%s.%s", Yii::$app->name, $name);
        return self::read('getChildren', [$name]);
    }

    public function getPermission($name)
    {
        $name =	sprintf("%s.%s", Yii::$app->name, $name);
        return self::read('getPermission', [$name]);
    }

    public function getPermissions()
    {
        return self::read('getPermissions');
    }

    public function getPermissionsByRole($roleName)
    {
        $roleName =	sprintf("%s.%s", Yii::$app->name, $roleName);
        return self::read('getPermissionsByRole', [$roleName]);
    }

    public function getPermissionsByUser($userId)
    {
        return self::read('getPermissionsByUser', [$userId]);
    }

    public function getRole($name)
    {
        $name =	sprintf("%s.%s", Yii::$app->name, $name);
        return self::read('getRole', [$name]);
    }

    public function getRoles()
    {
        return self::read('getRoles');
    }

    public function getRolesByUser($userId)
    {
        return self::read('getRolesByUser', [$userId]);
    }

    public function getRule($name)
    {
        $name =	sprintf("%s.%s", Yii::$app->name, $name);
        return self::read('getRule', [$name]);
    }

    public function getRules()
    {
        return self::read('getRules');
    }

    public function getUserIdsByRole($roleName)
    {
        $roleName =	sprintf("%s.%s", Yii::$app->name, $roleName);
        return self::read('getUserIdsByRole', [$roleName]);
    }

    public function hasChild($parent, $child)
    {
        return self::read('hasChild', [$parent, $child]);
    }

    public function remove($object)
    {
        return self::write('remove', [$object]);
    }

    public function removeAll()
    {
        return self::write('removeAll');
    }

    public function removeAllAssignments()
    {
        return self::write('removeAllAssignments');
    }

    public function removeAllPermissions()
    {
        return self::write('removeAllPermissions');
    }

    public function removeAllRoles()
    {
        return self::write('removeAllRoles');
    }

    public function removeAllRules()
    {
        return self::write('removeAllRules');
    }

    public function removeChild($parent, $child)
    {
        return self::write('removeChild', [$parent, $child]);
    }

    public function removeChildren($parent)
    {
        return self::write('removeChildren', [$parent]);
    }

    public function revoke($role, $userId)
    {
        return self::write('revoke', [$role, $userId]);
    }

    public function revokeAll($userId)
    {
        return self::write('revokeAll', [$userId]);
    }

    public function update($name, $object)
    {
        $name =	sprintf("%s.%s", Yii::$app->name, $name);
        return self::write('update', [$name, $object]);
    }

    protected static function write($method, $args = [])
    {
        return self::request($method, $args, false, true);
    }

    protected static function read($method, $args = [], $cache = false)
    {
        return self::request($method, $args, $cache);
    }

    protected static function request($method, $args = [], $cache = false, $rw = false)
    {
        foreach($args as $k => $obj)
        {
            if(is_object($obj))
            {
                $arr 			= ArrayHelper::toArray($obj);
                $arr['class']	= $obj->classNAme();
                $args[$k]		= $arr;
            }
        }

        $module = Module::getInstance();
        return $module->rbac($method, $args, $cache, $rw);
    }
}
