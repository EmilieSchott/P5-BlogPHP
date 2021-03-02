<?php 

namespace EmilieSchott\BlogPHP\Model;

abstract class Hydrate {
    
    public function __construct(array $datas) {
        $this->hydrate($datas);
    }

    public function hydrate (array $datas) {
        foreach ($datas as $key => $value) {
            $method = 'set'.\ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }
}
