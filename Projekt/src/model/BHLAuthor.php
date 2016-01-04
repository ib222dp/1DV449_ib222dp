<?php

class BHLAuthor {

    private $name;
    private $role;

    public function __construct($name, $role) {
        $this->name = $name;
        $this->role = $role;
    }

    public function getName() {
        return $this->name;
    }

    public function getRole() {
        return $this->role;
    }

}