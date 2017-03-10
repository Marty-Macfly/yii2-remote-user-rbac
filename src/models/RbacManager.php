<?php

namespace macfly\user\client\models;

use Yii;
use yii\helpers\ArrayHelper;

use macfly\user\client\Module;

class RbacManager implements \yii\rbac\ManagerInterface
{
	public function add ($object)
	{
		return self::request('add', [$object]);
	}

	public function addChild($parent, $child)
	{
		return self::request('addChild', [$parent, $child]);
	}

	public function assign($role, $userId)
	{
		return self::request('assign', [$role, $userId]);
	}

	public function canAddChild($parent, $child)
	{
		return self::request('canAddChild', [$parent, $child]);
	}

	public function checkAccess($userId, $permissionName, $params = [])
	{
		$permissionName =	sprintf("%s.%s", Yii::$app->name, $permissionName);
		return self::request('checkAccess', [$userId, $permissionName, $params]);
	}

	public function createPermission($name)
	{
		$name =	sprintf("%s.%s", Yii::$app->name, $name);
		return self::request('createPermission', [$name]);
	}

	public function createRole ($name)
	{
		$name =	sprintf("%s.%s", Yii::$app->name, $name);
		return self::request('createRole', [$name]);
	}

	public function getAssignment($roleName, $userId)
	{
		$roleName =	sprintf("%s.%s", Yii::$app->name, $roleName);
		return self::request('getAssignment', [$roleName, $userId]);
	}

	public function getAssignments($userId)
	{
		return self::request('getAssignments', [$userId]);
	}

	public function getChildRoles($roleName)
	{
		$roleName =	sprintf("%s.%s", Yii::$app->name, $roleName);
		return self::request('getChildRoles', [$roleName]);
	}

	public function getChildren($name)
	{
		$name =	sprintf("%s.%s", Yii::$app->name, $name);
		return self::request('getChildren', [$name]);
	}

	public function getPermission($name)
	{
		$name =	sprintf("%s.%s", Yii::$app->name, $name);
		return self::request('getPermission', [$name]);
	}

	public function getPermissions()
	{
		return self::request('getPermissions');
	}

	public function getPermissionsByRole($roleName)
	{
		$roleName =	sprintf("%s.%s", Yii::$app->name, $roleName);
		return self::request('getPermissionsByRole', [$roleName]);
	}

	public function getPermissionsByUser($userId)
	{
		return self::request('getPermissionsByUser', [$userId]);
	}

	public function getRole($name)
	{
		$name =	sprintf("%s.%s", Yii::$app->name, $name);
		return self::request('getRole', [$name]);
	}

	public function getRoles()
	{
		return self::request('getRoles');
	}

	public function getRolesByUser($userId)
	{
		return self::request('getRolesByUser', [$userId]);
	}

	public function getRule($name)
	{
		$name =	sprintf("%s.%s", Yii::$app->name, $name);
		return self::request('getRule', [$name]);
	}

	public function getRules()
	{
		return self::request('getRules');
	}

	public function getUserIdsByRole($roleName)
	{
		$roleName =	sprintf("%s.%s", Yii::$app->name, $roleName);
		return self::request('getUserIdsByRole', [$roleName]);
	}

	public function hasChild($parent, $child)
	{
		return self::request('hasChild', [$parent, $child]);
	}

	public function remove($object)
	{
		return self::request('remove', [$object]);
	}

	public function removeAll()
	{
		return self::request('removeAll');
	}

	public function removeAllAssignments()
	{
		return self::request('removeAllAssignments');
	}

	public function removeAllPermissions()
	{
		return self::request('removeAllPermissions');
	}

	public function removeAllRoles()
	{
		return self::request('removeAllRoles');
	}

	public function removeAllRules()
	{
		return self::request('removeAllRules');
	}

	public function removeChild($parent, $child)
	{
		return self::request('removeChild', [$parent, $child]);
	}

	public function removeChildren($parent)
	{
		return self::request('removeChildren', [$parent]);
	}

	public function revoke($role, $userId)
	{
		return self::request('revoke', [$role, $userId]);
	}

	public function revokeAll($userId)
	{
		return self::request('revokeAll', [$userId]);
	}

	public function update($name, $object)
	{
		$name =	sprintf("%s.%s", Yii::$app->name, $name);
		return self::request('update', [$name, $object]);
	}

	protected static function request($method, $args = [], $cache = false)
	{
		foreach($args as $k => $obj)
		{
			if(is_object($obj))
			{
				$arr 					= ArrayHelper::toArray($obj);
				$arr['class']	= $obj->classNAme();
				$args[$k]			= $arr;
			}
		}

    $module = Module::getInstance();
    return $module->rbac($method, $args, $cache);
	}
}
