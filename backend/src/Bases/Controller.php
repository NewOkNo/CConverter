<?php

namespace Src\Bases;

abstract class Controller{
    /**
     * The middleware registered on the controller.
     *
     * @var array
     */
    protected $middleware = [];
}