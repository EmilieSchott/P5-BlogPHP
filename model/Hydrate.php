<?php 

// Define namespace of this class :
namespace EmilieSchott\BlogPHP\Model;

// Define this class :
abstract class Hydrate {

    // Construct an instance
    
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
