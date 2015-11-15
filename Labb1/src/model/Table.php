<?php

class Table {

    private $time;
    private $value;

    public function __construct($time, $value) {
        $this->time = $time;
        $this->value = $value;
    }

    public function getTime() {
        return $this->time;
    }

    public function getValue() {
        return $this->value;
    }

}