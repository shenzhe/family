<?php
//file framework/Family/MVC/Entity.php
namespace Family\MVC;


class Entity
{
    /**
     * Entity constructor.
     * @param array $array
     * @desc 把数组填充到entity
     */
    public function __construct(array $array)
    {
        if (empty($array)) {
            return $this;
        }

        foreach ($array as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}