<?php
namespace Test;
class Test extends \Database\Base {
    public static function TableName() { return "mail"; }

    function __construct() {
        echo 'YAY!';
    }
}