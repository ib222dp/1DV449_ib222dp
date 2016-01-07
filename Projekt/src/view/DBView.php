<?php

class DBView
{
    private $model;

    public function __construct(DBModel $model) {
        $this->model = $model;
    }

}