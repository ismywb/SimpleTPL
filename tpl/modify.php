
<?php

lRequired();
$__output = '';

if (isset($_REQUEST['player'])) {
    $round = \Tournaments\Round::LoadBy(array('game_id'=>$_REQUEST['tid'],'round'=>$_REQUEST['round'],'player_id'=>$_REQUEST['player']))[0];
    
    if ($_REQUEST['do'] == 'minus') {
        if ($_REQUEST['flag'] == 'kills') {
           $round->kills = max(0,($round->kills -1));
            $round->Save();
        }
        
        if ($_REQUEST['flag'] == 'extrapoints') {
           $round->extra_points = $round->extra_points - 1;
           $round->Save();
        }
        
        if ($_REQUEST['flag'] == 'ieject') {
           $round->imp_eject = max($round->imp_eject - 1,0);
           $round->imp_vote = max($round->imp_vote - 1,0);
           $round->Save();
        }
        
        if ($_REQUEST['flag'] == 'ivote') {
           $round->imp_vote = max($round->imp_vote - 1,0);
           $round->Save();
        }
        
        if ($_REQUEST['flag'] == 'ceject') {
           $round->crew_eject = max($round->crew_eject - 1,0);
           $round->crew_vote = max($round->crew_vote - 1,0);
           $round->Save();
        }
        
        if ($_REQUEST['flag'] == 'cvote') {
           $round->crew_vote = max($round->crew_vote - 1,0);
           $round->Save();
        }
    }
    
    if ($_REQUEST['do'] == 'editnote') {
        $round->extra_points_why = $_REQUEST['why'];
        $round->Save();
    }
    
    if ($_REQUEST['do'] == 'plus') {
        if ($_REQUEST['flag'] == 'kills') {
           $round->kills = $round->kills + 1;
           $round->Save();
        }
        
        if ($_REQUEST['flag'] == 'extrapoints') {
           $round->extra_points = $round->extra_points + 1;
           $round->Save();
        }
        
        if ($_REQUEST['flag'] == 'ieject') {
           $round->imp_vote = $round->imp_vote + 1;
           $round->imp_eject = $round->imp_eject + 1;
           $round->Save();
        }
        
        if ($_REQUEST['flag'] == 'ivote') {
           $round->imp_vote = $round->imp_vote + 1;
           $round->Save();
        }
        
        if ($_REQUEST['flag'] == 'ceject') {
           $round->crew_eject = $round->crew_eject + 1;
           $round->crew_vote = $round->crew_vote + 1;
           $round->Save();
        }
        
        if ($_REQUEST['flag'] == 'cvote') {
           $round->crew_vote = $round->crew_vote + 1;
           $round->Save();
        }
    }
    
    
    if ($_REQUEST['do'] == 'toggle') {
        if ($_REQUEST['flag'] == 'iscrew') {
           if ($round->is_crew == 2) 
             $round->is_crew = 0;
           $round->is_crew = \flipBitWise($round->is_crew);
           $round->Save();
        }
        
        if ($_REQUEST['flag'] == 'isneutral') {
           $round->is_crew = 2;
           $round->Save();
        }
        
        if ($_REQUEST['flag'] == 'tdone') {
           $round->tasks_done = \flipBitWise($round->tasks_done);
           $round->Save();
        }
    }

}
if ($_REQUEST['do'] == 'win') {
    if (isset($_REQUEST['crewWin'])) {
        $match = \Tournaments\Match::LoadBy(array('tid'=>$_REQUEST['tid'],'round'=>$_REQUEST['round']))[0];
        $match->is_crew_win = $_REQUEST['crewWin'];
        $match->Save();
    }
    if (isset($_REQUEST['taskWin'])) {
        $match = \Tournaments\Match::LoadBy(array('tid'=>$_REQUEST['tid'],'round'=>$_REQUEST['round']))[0];
        $match->is_task_win = $_REQUEST['taskWin'];
        $match->Save();
        if ($_REQUEST['taskWin'] == 1) {
        $round = \Tournaments\Round::LoadBy(array('game_id'=>$_REQUEST['tid'],'round'=>$_REQUEST['round'],'is_crew'=>1));
        foreach($round as $player) {
            $player->tasks_done = 1;
            $player->Save();
        }}
    }
    
    if (isset($_REQUEST['saboWin'])) {
        $match = \Tournaments\Match::LoadBy(array('tid'=>$_REQUEST['tid'],'round'=>$_REQUEST['round']))[0];
        $match->is_sabo_win = $_REQUEST['saboWin'];
        $match->Save();
    }
}
//print_r($round);
//die;
if ($_REQUEST['flag'] == 'remove') {
    $player = new \Players\Player($_REQUEST['pid']);
    $tourn = new \Tournaments\Tournament($_REQUEST['tid']);
    if ($_REQUEST['confirm'] == 1) {
        $votes = \Tournaments\Round::LoadBy(array('game_id'=>$_REQUEST['tid'],'player_id'=>$_REQUEST['pid']));
        foreach($votes as $vote) {
            $vote->Delete();

        }
    } else {
        define('STAY',1);
        $tpl = <<<end
<h1>This will remove a player from the tournament and remove all votes!</h1>
If you wish to remove {$player->name} from the tournament <code>$tourn->name</code> please click <a href="/modify.html?pid={$_REQUEST['pid']}&tid={$_REQUEST['tid']}&round={$_REQUEST['round']}&flag=remove&confirm=1">here</a>
end;
$__output = $tpl;
    }
}

if ($_REQUEST['flag'] == 'removeT') {
lRequired(5);
    try {
        $tourn = new \Tournaments\Tournament($_REQUEST['tid']);
        $id = $tourn->id;
        if ($_REQUEST['confirm'] == 1) {
            try {
                $votes = \Tournaments\Round::LoadBy(array('game_id'=>$_REQUEST['tid']));
                foreach($votes as $vote) {
                    $vote->Delete();
                }
            } catch (\Exceptions\ItemNotFound $e){}
            try {
                $matches = \Tournaments\Match::LoadBy(array('tid'=>$_REQUEST['tid']));
                foreach($matches as $match) {
                    $match->Delete();
                }
            } catch (\Exceptions\ItemNotFound $e){}
            $tourn->Delete();
            header("Location: /manageT.html");
            die;

        } else {
            define('STAY',1);
            $tpl = "<h1>This will completely delete a tournament!</h1>If you wish to remove the tournament <code>$tourn->name</code> please click <a href='/modify.html?tid={$_REQUEST['tid']}&flag=removeT&confirm=1'>here</a>";
            $__output = $tpl;
        }
    } catch (\Exceptions\ItemNotFound $e){}
}
// ALL DONE, GO BACK
if (!defined('STAY')) {
    header("Location: /round.html?tid={$_REQUEST['tid']}&round={$_REQUEST['round']}");
    die;
}
