<?php
namespace Tournaments;
class Podium extends \Database\Base {
    public static function TableName() { return "players"; }

    public static function getPlayers() {
        $first = self::_LoadBySQL('Select * from `players` where `first` != 0 OR `second` != 0 OR `third` != 0');
        return $first;
    }
    
    public function first() {
        $out = '<p style="display: none;">'.$this->first.'</p>';
        for ($i = 0; $i < $this->first; $i++) {
            $out .= "&nbsp;<img class='podium-icon' src='/img/first.png' />";
        }
        if ($this->first == 0) {
            $out .= "&nbsp;<img class='podium-icon' src='/img/none.png' />";
        }
        return $out;
    }
    
    public function second() {
        $out = '<p style="display: none;">'.$this->second.'</p>';
        for ($i = 0; $i < $this->second; $i++) {
            $out .= "&nbsp;<img class='podium-icon' src='/img/second.png' />";
        }
        if ($this->second == 0) {
            $out .= "&nbsp;<img class='podium-icon' src='/img/none.png' />";
        }
        return $out;
    }
    
    public function third() {
        $out = '<p style="display: none;">'.$this->third.'</p>';
        for ($i = 0; $i < $this->third; $i++) {
            $out .= "&nbsp;<img class='podium-icon' src='/img/third.png' />";
        }
        if ($this->third == 0) {
            $out .= "&nbsp;<img class='podium-icon' src='/img/none.png' />";
        }
        return $out;
    }

}
