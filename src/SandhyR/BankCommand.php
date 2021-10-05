<?php

namespace SandhyR;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class BankCommand extends Command{

    private $plugin;

    public function __construct(string $name, string $description, Main $owner)
    {
        parent::__construct($name, $description);
        $this->plugin = $owner;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if($sender instanceof Player){
            $this->plugin->bankgui($sender);
        } else {
            $sender->sendMessage("You not a player");
        }
    }
}