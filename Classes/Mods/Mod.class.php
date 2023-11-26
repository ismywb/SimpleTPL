<?php
namespace Mods;
class Mod extends \Database\Base {
    public static function getMod($id = 1) {
        $mod = new Mod($id);
        return $mod->name;
    }
	
    public static function getMods() {
	$data = self::LoadBy();
	return $data;
    }

    public static function getRoles($mod_id, $type = -1) {
      $roles = \Mods\Role::getRoles($mod_id, $type);
      return $roles;
    }

    

    public static function getRole($mod_id,$role_id,$r = NULL) {
//       if (is_null($role_id)) return "Unknown";// <!-- ".print_r(self::getRoles($mod_id),1)." -->";
      try {
        $role = \Mods\Role::LoadBy(array("mod_id"=>$mod_id,"id"=>$role_id))[0];
        return array("name"=>$role->name,"hex"=>$role->hex);
      } catch (\Exceptions\ItemNotFound $e) { return array("name"=>"Crewmate","hex"=>'#c3c3ef'); }
    }

    public static function getDropDown($mod_id, $role_id) {
      $dropdown = '';
      $dropdown .= "<option value='null'>Crewmate</option>";
      $dropdown .= self::getDropDown2($mod_id,$role_id,0);
      $dropdown .= self::getDropDown2($mod_id,$role_id,1);
      $dropdown .= self::getDropDown2($mod_id,$role_id,2);
      return $dropdown;
    }

    public static function getDropDown2($mod_id, $role_id,$type) {
      $roles = self::getRoles($mod_id, $type);
      $dropdown = '';
      foreach ($roles as $role) {
        $sel = "";
        if ($role->id == $role_id) $sel = " selected='yes' ";
        $dropdown .= <<<end
<option style="background-color: {$role->hex}" value="{$role->id}"{$sel}>{$role->name}</option>

end;
      }
      return $dropdown;
    }

    public static function getPackDropDown($mod_id = -1) {
	$mods = self::getMods();
      $dropdown = '';
      foreach ($mods as $role) {
        $sel = "";
        if ($role->id == $mod_id) $sel = " selected='yes' ";
        $dropdown .= "<option {$sel} value='{$role->id}'>{$role->name}</option>";
      }
      return $dropdown;
    }
	
    public static function TableName() { return "modpacks"; }

}
