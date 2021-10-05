<?php

namespace SandhyR;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;

class EventListener implements Listener{

    private $plugin;

    public function __construct(Main $owner){
        $this->plugin = $owner;
    }

    public function onJoin(PlayerJoinEvent $event)
    {
        $player = $event->getPlayer();
        $playername = $player->getName();
        $p = $this->plugin->getDatabase()->query("SELECT username FROM bank where username='$playername'");
        $player = mysqli_fetch_array($p);
        if ($player == null) {
            $this->plugin->getDatabase()->query("INSERT INTO bank VALUES(null , '$playername', 0)");
        }
    }

    public function onChat(PlayerChatEvent $event){
        $player = $event->getPlayer();
        if(isset($this->plugin->withdraw[$player->getLowerCaseName()])){
            $this->plugin->withdrawcustom($player, $event->getMessage());
        }
        if(isset($this->plugin->deposit[$player->getLowerCaseName()])){
            $this->plugin->depositcustom($player, $event->getMessage());
        }
    }
}