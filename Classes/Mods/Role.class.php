<?php
namespace Mods;
class Role extends \Database\Base {
    public static function TableName() { return "roles"; }
    public static function getRoles($mod_id,$type = -1 ) {
      if ($type == -1) {
        return self::LoadBy(["mod_id"=>$mod_id]);
      } else {
        return self::LoadBy(["mod_id"=>$mod_id,"crew_type"=>$type]);
      }
    }
}
