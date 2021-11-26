<?php

class Bar{
    private $foo;
    private $name;

    public function __construct($name = "Poisson", Foo $foo){
        $this->foo = $foo;
        $this->name = $name;
    }
}