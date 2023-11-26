<?php
namespace Players;
class Player extends \Database\Base {
    public static function TableName() { return "players"; }
    public static function getUserByTag($tag) {
        return self::LoadBy(array('discord_tag'=>$tag));
    }

}