<?php
/**
 * @author Juriy Panasevich <juriy.panasevich@gmail.com>
 */

namespace common\models;


use bigdropinc\sti\ActiveRecord;

/**
 * Class StiActiveRecord
 * @package common\models
 */
class StiActiveRecord extends ActiveRecord
{

    protected static function isBaseClass($class)
    {
        $parentClass = get_parent_class($class);
        if ($class === $parentClass) {
            return static::isBaseClass($parentClass);
        }
        return $parentClass === self::class;
    }

    protected static function getStiValue($className = null)
    {
        return $className ?: static::class;
    }

    public static function instantiate($row)
    {
        if (isset($row[static::getStiColumn()])) {
            $className = $row[static::getStiColumn()];
            if (class_exists($className)) {
                return new $className;
            }
        }
        return parent::instantiate($row);
    }
}
