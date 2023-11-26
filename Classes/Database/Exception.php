<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Database;

/**
 * Description of Exception
 *
 * @author Jay
 */
class Exception extends \Exceptions\Base {
    public function __construct($message = null, $code = 0) {
        parent::__construct($message, $code);
    }
    public function Query() { return $this->_query; }
}
