<?php

namespace SandhyR;

use pocketmine\scheduler\Task;

class BungaTask extends Task{

    private $plugin;

    public function __construct(Main $owner)
    {
        $this->plugin = $owner;
    }

    public function onRun(int $currentTick)
    {
        $hour = date('H');
        var_dump($hour);
        if($hour == 00){
            $allmoney = $this->plugin->getDatabase()->query("SELECT money FROM bank");
            $allmoney = mysqli_fetch_row($allmoney);
            foreach($allmoney as $playersmoney){
                $persentase = 1 / 100;
                $money = $playersmoney * $persentase;
                $money = $playersmoney - $money;
                $this->plugin->getDatabase()->query("UPDATE bank SET money=$money");
            }
        }
    }
}


