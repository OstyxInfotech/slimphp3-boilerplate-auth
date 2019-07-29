<?php

namespace App\Controllers;

use Interop\Container\ContainerInterface;

abstract class Controller
{
    protected $c;

    public function __construct(ContainerInterface $c)
    {
        $this->c=$c;
    }

    public function __get($property)
    {
        if($this->c->{$property}){
            return $this->c->{$property};
        }
    }
}