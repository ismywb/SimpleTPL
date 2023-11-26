<?php
namespace Tournaments;
class Round  extends \Database\Base {
    //// POINTS
    //ublic static var $
    public static $ImpKillPoints = 3;
    public static $ImpWinPoints = 2;
    public static $ImpSaboBonus = 2;
    public static $CrewWin = 5;
    public static $CrewEjection = -2;
    public static $CrewVote = -1;
    public static $crewTasksDone = 2;
    public static $ImpVote = 1;
    public static $ImpEjection = 3;
    public static $CrewTaskBonus = 1;
    public static $CrewTaskWin = 6;
    
    public function isCrew($img = 1) {
        if ($img == 1) {
            if ($this->is_crew == 1) {
                return '<img class="icon" src="//static.scumscyb.org/img/yes.png" />';
            } else {
                return '<img class="icon" src="//static.scumscyb.org/img/none.png" />';
            }
        } else {
            if ($this->is_crew == 1) {
                return 1;
            } else {
                return 0;
            }
        }
    }
    
    public function isImp($img = 1) {
        
        if ($img == 1) {
            if ($this->is_crew == 0) {
                return '<img class="icon" src="//static.scumscyb.org/img/yes.png" />';
            } else {
                return '<img class="icon" src="//static.scumscyb.org/img/none.png" />';
            }
        } else {
            if ($this->is_crew == 0) {
                return 1;
            } else {
                return 0;
            }
        }
    }    
    
    public function isNeutral($img = 1) {
        
        if ($img == 1) {
            if ($this->is_crew == 2) {
                return '<img class="icon" src="//static.scumscyb.org/img/yes.png" />';
            } else {
                return '<img class="icon" src="//static.scumscyb.org/img/none.png" />';
            }
        } else {
            if ($this->is_crew == 2) {
                return 1;
            } else {
                return 0;
            }
        }
    }
    
    public function tasksDone() {
        if ($this->tasks_done) return '<img class="icon" src="//static.scumscyb.org/img/yes.png" />';
        else return '<img class="icon" src="//static.scumscyb.org/img/none.png" />';
    }
    
    function flipBitwise($a) {
        return -($a-1);
    }
    
    public function populatePlayer() {
        if (is_object($this->player_id)) return;
        $id = $this->player_id;
        $this->player_id = new \Players\Player($id);
       // $this->game_id = new \Tournaments\Match($this->game_id);
    }
    
    
    function getPoints($tid,$round) { //kills, impEject, impVote, crewEject, crewVote, isCrew, tasksDone, isCrewWin, isTaskWin, isSaboWin) {
        $sql = "SELECT * FROM `matches` WHERE `tid` = '".$tid."' AND `round` = '".$round."'";
        $match = \Tournaments\Match::_LoadBySQL($sql)[0];
        if ($this->is_crew == 0) {
            return $this->getImpPoints($this->kills, $match->is_crew_win, $match->is_sabo_win);
        } else {
            return $this->getCrewPoints($match);
        }
}

    private function getCrewPoints($match) {
        $crewVotePoints = ( $this->crew_vote * self::$CrewVote);
        $crewEjectPoints = ( $this->crew_eject * self::$CrewEjection );
        $impVotePoints = ( $this->imp_vote * self::$ImpVote );
        $impEjectPoints = ( $this->imp_eject * self::$ImpEjection );

        $WinPoints = 0;
        if ($match->is_crew_win == 1 && $this->is_crew == 1) {
            $WinPoints = self::$CrewWin;
        }
        
        if ($match->is_task_win && $this->is_crew == 1) {
            $WinPoints = $WinPoints + self::$CrewTaskWin;
        }
        $votePoints =  $impVotePoints + $impEjectPoints;
        if ($this->is_crew == 1) $votePoints += ($crewVotePoints + $crewEjectPoints);
        if ($this->tasks_done) {
            $WinPoints = $WinPoints + self::$crewTasksDone;
        }

	/*
	if ($this->is_crew == 2) */
	$WinPoints += ( $this->kills * self::$ImpKillPoints);

        return ($WinPoints + $votePoints + $this->extra_points);
    }
    public static function TableName() { return "voting"; }
    
    function getImpPoints($kills, $win, $saboWin ) {
      //  die(print_r(array($kills,$win,$saboWin),1));

        if ($win == 2) {
            $win = 0;
        } else {
            $win = \flipBitwise($win);
        }
        $KillPoints = ($kills * self::$ImpKillPoints);
        $WinPoints = 0;
        if ($win == 1) {
            $WinPoints = self::$ImpWinPoints;
        }
        $SaboWinPoints = 0;
        if ($saboWin == 1) {
            $SaboWinPoints = self::$ImpSaboBonus;
        }
               // echo "<!--\n\n".print_r($match,1)."$KillPoints + $WinPoints + $SaboWinPoints + $this->extra_points \n\n-->";        

        return($KillPoints + $WinPoints + $SaboWinPoints + $this->extra_points);
    }
    

    /*
    public function getPoints($player,$round) {
        $points = 0;
        
      $match = \Tournaments\Match::_LoadBySQL("SELECT * FROM `matches` WHERE `tid` = 1 AND `round` = 1")[0];
      //$match = \Tournaments\Match::LoadBy([['tid'=>'{$this->game_id}','round'=>'{$round}']]);
      
        return print_r($match,1);
        
    }*/
    

}
