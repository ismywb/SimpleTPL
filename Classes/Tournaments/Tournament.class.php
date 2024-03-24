<?php
namespace Tournaments;
class Tournament extends \Database\Base {
    public static function TableName() { return "games"; }
    
    public function getFinalPoints($player_id,$upto = 10) {
        $points = 0;
        $data = array(
            'game_id'=> $this->id,
            'player_id' => $player_id,
            'round' => array('<=',$upto)
        );
       
        $round = \Tournaments\Round::LoadBy($data);
       
       // echo "<!--\n\nFinal Points: \n".print_r($round,1)."\n\n-->";
        $roundEnum = 1;
        $rounds = array();
        foreach($round as $r) {
            $rounds[$r->round] = $r;
        }
        sort($rounds);
       // echo "<!--\n\nFinal Points: \n".print_r($rounds,1)."\n\n-->";
        foreach($rounds as $r) {
            
           $rPoints = $r->getPoints($this->id,$r->round);//$roundEnum);
//           echo "<!--\n\nRound: {$roundEnum}\n".print_r($r,1)."\n {$rPoints}\n\n-->"; 

           $points += $rPoints;
            $roundEnum++;
        }
        return $points;
    }
    
    function getMost($type = 'kills') {
        $_sql = 'SELECT sum('.$type.') as count, player_id FROM `voting` where game_id = '.$this->id.' GROUP BY player_id order by count desc limit 1';
        $result = self::_LoadBySQL($_sql)[0];
        $player = new \Players\Player($result->player_id);
        return (object)array("player"=>$player,"count"=>$result->count);
        //return ($result);
        
    }
    
    public static function getMostAllTime($type = 'kills') {
        $_sql = 'SELECT sum('.$type.') as count, player_id FROM `voting` GROUP BY player_id order by count desc limit 1';
        $result = self::_LoadBySQL($_sql)[0];
        $player = new \Players\Player($result->player_id);
        return (object)array("player"=>$player,"count"=>$result->count);
        //return ($result);
        
    }
    
    public function isPublic() {
        if ($this->public == 1) return '<img class="icon" src="/img/yes.png" />';
        else return '<img class="icon" src="/img/none.png" />';
    }
    
    public function getPlayers() {
        try {
            $players = \Tournaments\Round::_LoadBySQL("Select DISTINCT `player_id` from `voting` where `game_id` = '{$this->id}' AND `round` = '1'");
            
            $playerArray = array();
            foreach($players as $player) {
                $user = new \Players\Player($player->player_id);
                $playerArray[$user->id] = $this->getFinalPoints($user->id);
            }
            return $playerArray;
        } catch (\Exceptions\ItemNotFound $e) { return array(); }
    }
}
