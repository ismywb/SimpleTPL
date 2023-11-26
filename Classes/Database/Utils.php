<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Database;

/**
 * Description of Utils
 *
 * @author Jay
 */
class Utils {
    public static function FrameworkTablenames() {
        return array(
            "badges","boat_types", "chat_emotes", "chest_type_items", "chest_types", "citys",
            "competition_event_type_modifiers", "competition_event_types", "competition_notification_types",
            "competition_type", "competition_type_durations", "crimes", "deed_types", "donator_packages", "donor_groups",
            "gang_purchase_rewards","houses", "item_sections", "item_types", 
            "item_types_autobenefit", "item_types_stealable", "jobs", "marketing_channels", "marketing_gift_registrations",
            "modifier", "npc_types", "npc_type_spawn_options", "npc_type_spawn_ports", "plugins", "point_shop_items","procs", "profession_sections", 
            "profession_skills", "profession_types", "quests", "scavenger_hunt", "school", 
            "shops", "tier_set", "vote_urls"
        );
    }
    public static function FrameworkTablesAllTrue() {
        $ret = array();
        foreach(static::FrameworkTablenames() as $tbl) {
            $ret[$tbl] = true;
        }
        return $ret;
    }
    public static function Sync($tables, $fromServerName = 'arena.ruletheseas.com', $toServerName = 'www.ruletheseas.com') {
        $from = \Command\Server::GetServer($fromServerName);
        $to   = \Command\Server::GetServer($toServerName);
        foreach(static::FrameworkTablenames() as $tablename) {
            if(!isset($tables[$tablename]) || !$tables[$tablename]) { continue; }
            try { static::CopyTo($from->DBConnection(), $to->DBConnection(), $tablename); } catch(\Database\Exception $ex) {
                
            }
        }
    }
    public static function LogError($query, $file, $line, $throw = false) {
        \Command\Log::Log(print_r($query->errorInfo(), true), $file, $line);
        \Command\Log::Log(print_r($query, true), $file, $line);
        if($throw) {
            throw new \Database\Exception($query);
        }
    }
    public static function CopyTo($fromConnection, $toConnection, $tablename) {
        $sql1 = "SELECT * FROM $tablename";
        $q1 = $fromConnection->prepare($sql1);
        $sql2 = "REPLACE INTO $tablename SET ";
        $q2 = false;
        if(!$q1->execute()) { static::LogError($q1, __FILE__, __LINE__, true); }
        while($row = $q1->fetch(\PDO::FETCH_ASSOC)) {
            $vals = array();
            foreach($row as $col=>$val) {
                if(!$q2) { $sql2 .= "$col = :$col, "; }
                $vals[":$col"] = $val;
            }
            if(!$q2) {
                $sql2 = substr($sql2, 0, -2);
                $q2 = $toConnection->prepare($sql2);
                //echo "prepared $sql2";
            }
            // Don't insert where ID is 0, it duplicates the row...
            if(isset($vals[":id"]) && !$vals[":id"]) { \Command\Log::Log("row in $tablename with 0 id: ".print_r($vals, true), __FILE__, __LINE__); continue; }
            if(!$q2->execute($vals)) { static::LogError($sql2, __FILE__, __LINE__, true); }
        }
    }
    public static function Run($sql, $params = null, $conn = null) {
        if(!$conn) { $conn = \DBConnectionFactory::getFactory()->getConnection(); }
        try {
            $q = $conn->prepare($sql);
            return $q->execute($params);
        } catch (\PDOException $ex) {
            \Command\Log::Log("Error processing $sql! Moving on... \n" . print_r($ex, true), __FILE__, __LINE__);
        }
    }
    public static function GetFirstRow($sql, $params = null, $conn = null, $perRow = null, $groupOn = null) {
        $all = static::GetArray($sql, $params, $conn, $perRow, $groupOn);
        return $all[0];
    }
    public static function GetArray($sql, $params = null, $conn = null, $perRow = null, $groupOn = null) {
        if(!$conn) { $conn = \DBConnectionFactory::getFactory()->getConnection(); }
        if(!is_callable($groupOn)) { $groupOn = function (&$rows, &$row) { $rows[] = $row; }; }
        $rows = array();
        try {
            $q = $conn->prepare($sql);
            if (!$q->execute($params)) { var_dump($q->errorInfo()); return $rows; }
            while ($row = $q->fetch(\PDO::FETCH_ASSOC)) {
                if(is_callable($perRow)) { $perRow($row); }
                $groupOn($rows, $row);
            }
        } catch (\PDOException $ex) {
            \Command\Log::Log("Error processing $sql! Moving on... \n" . print_r($ex, true), __FILE__, __LINE__);
        }
        return $rows;
    }
}
