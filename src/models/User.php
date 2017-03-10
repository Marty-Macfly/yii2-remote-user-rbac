<?php

namespace macfly\user\client\models;

use Yii;
use yii\base\Model;

use macfly\user\client\Module;

class User extends Model implements \yii\web\IdentityInterface
{
	public $attributeList = ['id', 'username', 'email'];
	public $timezone;

	/**
	 * @var array attribute values indexed by attribute names
	 */
	private $_attributes = [];

	/**
	* Returns the list of all attribute names of the record.
	* @return array list of attribute names.
	*/
	public function attributes()
	{
		return $this->attributeList;
	}

	/**
	 * Returns a value indicating whether the model has an attribute with the specified name.
	 * @param string $name the name of the attribute
	 * @return bool whether the model has an attribute with the specified name.
	 */
	public function hasAttribute($name)
	{
		return isset($this->_attributes[$name]) || in_array($name, $this->attributes(), true);
	}

	/**
	 * Returns the named attribute value.
	 * `null` will be returned.
	 * @param string $name the attribute name
	 * @return mixed the attribute value. `null` if the attribute is not set or does not exist.
	 * @see hasAttribute()
	 */
	public function getAttribute($name)
	{
		return isset($this->_attributes[$name]) ? $this->_attributes[$name] : null;
	}

	/**
	 * Sets the named attribute value.
	 * @param string $name the attribute name
	 * @param mixed $value the attribute value.
	 * @throws InvalidParamException if the named attribute does not exist.
	 * @see hasAttribute()
	 */
	public function setAttribute($name, $value)
	{
		if ($this->hasAttribute($name))
		{
			$this->_attributes[$name] = $value;
		} else
		{
			throw new InvalidParamException(get_class($this) . ' has no attribute named "' . $name . '".');
		}
	}

	/**
	 * @inheritdoc
	 */
	public function canGetProperty($name, $checkVars = true, $checkBehaviors = true)
	{
		if (parent::canGetProperty($name, $checkVars, $checkBehaviors))
		{
			return true;
		}

		try
		{
			return $this->hasAttribute($name);
		} catch (\Exception $e)
		{
			// `hasAttribute()` may fail on base/abstract classes in case automatic attribute list fetching used
			return false;
		}
	}

	/**
	 * @inheritdoc
	 */
	public function canSetProperty($name, $checkVars = true, $checkBehaviors = true)
	{
		if (parent::canSetProperty($name, $checkVars, $checkBehaviors))
		{
			return true;
		}

		try
		{
			return $this->hasAttribute($name);
		} catch (\Exception $e)
		{
			// `hasAttribute()` may fail on base/abstract classes in case automatic attribute list fetching used
			return false;
		}
	}

	/**
	 * PHP getter magic method.
	 * This method is overridden so that attributes and related objects can be accessed like properties.
	 *
	 * @param string $name property name
	 * @throws \yii\base\InvalidParamException if relation name is wrong
	 * @return mixed property value
	 * @see getAttribute()
	 */
	public function __get($name)
	{
		if(isset($this->_attributes[$name]) || array_key_exists($name, $this->_attributes))
		{
			return $this->_attributes[$name];
		} elseif($this->hasAttribute($name))
		{
			return null;
		} else
		{
			return parent::__get($name);
		}
	}

	/**
	 * PHP setter magic method.
	 * This method is overridden so that attributes can be accessed like properties.
	 * @param string $name property name
	 * @param mixed $value property value
	 */
	public function __set($name, $value)
	{
		if ($this->hasAttribute($name))
		{
			$this->_attributes[$name] = $value;
		} else
		{
#			parent::__set($name, $value);
		}
	}

	/**
	 * Checks if a property value is null.
	 * This method overrides the parent implementation by checking if the named attribute is `null` or not.
	 * @param string $name the property name or the event name
	 * @return bool whether the property value is null
	 */
	public function __isset($name)
	{
		try
		{
			return $this->__get($name) !== null;
		} catch (\Exception $e)
		{
			return false;
		}
	}

	/**
	 * Sets a component property to be null.
	 * This method overrides the parent implementation by clearing
	 * the specified attribute value.
	 * @param string $name the property name or the event name
	 */
	public function __unset($name)
	{
		if ($this->hasAttribute($name))
		{
			unset($this->_attributes[$name]);
		} else
		{
			parent::__unset($name);
		}
	}

  public function setTimezone($value)
  { 
		$this->timezone	= $value;

		if(!is_null($this->timezone))
		{
			\Yii::$app->timeZone	=	$this->timezone;
		}
  }

  protected static function request($method, $args = [], $cache = false)
  {
    $module = Module::getInstance();
    return $module->identity($method, $args, $cache);
  }

  public static function findIdentity($id)
  {
    return self::request('findIdentity', [$id], true);
  }

  public static function findIdentityByAccessToken($token, $type = null)
  {
    return self::request('findIdentityByAccessToken', [$token, $type], true);
  }

  public function getId()
  {
    return self::request('getId', true);
  }

  public function getAuthKey()
  {
    return self::request('getAuthKey', true);
  }

  public function validateAuthKey($authKey)
  {
    return self::request('validateAuthKey', [$authKey], true);
  }
}
