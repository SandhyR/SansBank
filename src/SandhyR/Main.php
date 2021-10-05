<?php

namespace SandhyR;

use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use libs\muqsit\invmenu\InvMenu;
use libs\muqsit\invmenu\InvMenuHandler;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\event\Listener;
use SandhyR\BankCommand;
use onebone\economyapi\EconomyAPI;

class Main extends PluginBase implements Listener
{
    private $gui;
    private $config;
    public $withdraw;
    public $deposit;

    public function onEnable()
    {
        @mkdir($this->getDataFolder());
        $this->saveDefaultConfig();
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML, array());
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->getDatabase()->query("CREATE TABLE IF NOT EXISTS bank ( id INT PRIMARY KEY AUTO_INCREMENT , username VARCHAR(255) NOT NULL , money INT(11) NOT NULL);");
        $this->getServer()->getCommandMap()->register("SansBank", new BankCommand("bank", "Sans SMP Bank", $this));
        $this->gui = InvMenu::create(InvMenu::TYPE_CHEST);
        $this->getLogger()->info("Plugin Active");
        if (!InvMenuHandler::isRegistered()) {
            InvMenuHandler::register($this);
        }
    }

    public function getDatabase()
    {
        return new \mysqli($this->config->get("host"), $this->config->get("user"), $this->config->get("password"), $this->config->get("db-name"));
    }

    public function bankgui(Player $player)
    {
        $this->gui->readonly();
        $this->gui->setListener([$this, "banklistener"]);
        $this->gui->setName(TextFormat::GRAY . TextFormat::BOLD . "[" . TextFormat::AQUA . "Sans" . TextFormat::GOLD . "Bank" . TextFormat::GRAY . "]");
        $inventory = $this->gui->getInventory();
        $inventory->setItem(0, Item::get(160, 11, 1));
        $inventory->setItem(1, Item::get(160, 11, 1));
        $inventory->setItem(2, Item::get(160, 11, 1));
        $inventory->setItem(3, Item::get(160, 13, 1));
        $inventory->setItem(4, Item::get(160, 13, 1));
        $inventory->setItem(5, Item::get(160, 13, 1));
        $inventory->setItem(6, Item::get(160, 11, 1));
        $inventory->setItem(7, Item::get(160, 11, 1));
        $inventory->setItem(8, Item::get(160, 11, 1));
        $inventory->setItem(9, Item::get(160, 11, 1));
        $inventory->setItem(10, Item::get(266, 0, 1)->setCustomName(TextFormat::GRAY . "<------[" . TextFormat::GREEN . "Withdraw" . TextFormat::GRAY . "]------>" . "\n\n" . TextFormat::YELLOW . "Klik Untuk Penarikan Uang dari saldo bank mu"));
        $inventory->setItem(11, Item::get(160, 11, 1));
        $inventory->setItem(12, Item::get(160, 13, 1));
        $inventory->setItem(13, Item::get(266, 0, 1)->setCustomName(TextFormat::GRAY . "<------[" . TextFormat::YELLOW . "Balance" . TextFormat::GRAY . "]------>" . "\n\n" . TextFormat::AQUA . "Saldo: " . TextFormat::GREEN . $this->getBalance($player) . "\n\n" . TextFormat::YELLOW . "Kamu akan mendapat uang bonus pada saldo bankmu setiap hari jam 12 malam!" . "\n\n" . TextFormat::LIGHT_PURPLE . "Bunga bank" . TextFormat::WHITE . ": " . TextFormat::RED . "1" . "\n" . TextFormat::DARK_PURPLE . "Potensi Bonus" . TextFormat::WHITE . ": " . TextFormat::GREEN . "+1"));
        $inventory->setItem(14, Item::get(160, 13, 1));
        $inventory->setItem(15, Item::get(160, 11, 1));
        $inventory->setItem(16, Item::get(266, 0, 1)->setCustomName(TextFormat::GRAY . "<------[" . TextFormat::GREEN . "Deposit" . TextFormat::GRAY . "]------>" . "\n\n" . TextFormat::YELLOW . "Klik untuk memasukan uang ke saldo bankmu"));
        $inventory->setItem(17, Item::get(160, 11, 1));
        $inventory->setItem(18, Item::get(160, 11, 1));
        $inventory->setItem(19, Item::get(160, 11, 1));
        $inventory->setItem(20, Item::get(160, 11, 1));
        $inventory->setItem(21, Item::get(160, 13, 1));
        $inventory->setItem(22, Item::get(160, 13, 1));
        $inventory->setItem(23, Item::get(160, 13, 1));
        $inventory->setItem(24, Item::get(160, 11, 1));
        $inventory->setItem(25, Item::get(160, 11, 1));
        $inventory->setItem(26, Item::get(160, 11, 1));
        $this->gui->send($player);
    }

    public function withdrawgui(Player $player)
    {
        $this->gui->readonly();
        $this->gui->setListener([$this, "banklistener"]);
        $this->gui->setName(TextFormat::GRAY . TextFormat::BOLD . "[" . TextFormat::AQUA . "Sans" . TextFormat::GOLD . "Bank" . TextFormat::GRAY . "]");
        $inventory = $this->gui->getInventory();
        $inventory->setItem(0, Item::get(160, 14, 1));
        $inventory->setItem(1, Item::get(160, 14, 1));
        $inventory->setItem(2, Item::get(160, 14, 1));
        $inventory->setItem(3, Item::get(160, 14, 1));
        $inventory->setItem(4, Item::get(160, 14, 1));
        $inventory->setItem(5, Item::get(160, 14, 1));
        $inventory->setItem(6, Item::get(160, 14, 1));
        $inventory->setItem(7, Item::get(160, 14, 1));
        $inventory->setItem(8, Item::get(160, 14, 1));
        $inventory->setItem(9, Item::get(160, 14, 1));
        $inventory->setItem(10, Item::get(266, 0, 64)->setCustomName(TextFormat::GRAY . "<------[" . TextFormat::GREEN . "Withdraw 100" . TextFormat::GRAY . "]------>" . "\n\n" . TextFormat::YELLOW . "Klik Untuk Menarik semua uang ke dompetmu"));
        $inventory->setItem(11, Item::get(160, 13, 1));
        $inventory->setItem(12, Item::get(160, 13, 1));
        $inventory->setItem(13, Item::get(266, 0, 32)->setCustomName(TextFormat::GRAY . "<------[" . TextFormat::GREEN . "Withdraw 50" . TextFormat::GRAY . "]------>" . "\n\n" . TextFormat::YELLOW . "Klik untuk menarik setengah uang ke dompetmu"));
        $inventory->setItem(14, Item::get(160, 13, 1));
        $inventory->setItem(15, Item::get(160, 13, 1));
        $inventory->setItem(16, Item::get(266, 0, 1)->setCustomName(TextFormat::GRAY . "<------[" . TextFormat::GREEN . "Withdraw" . TextFormat::GRAY . "]------>" . "\n\n" . TextFormat::YELLOW . "Klik untuk menarik uang ke dompetmu dengan nominal custom"));
        $inventory->setItem(17, Item::get(160, 14, 1));
        $inventory->setItem(18, Item::get(160, 14, 1));
        $inventory->setItem(19, Item::get(160, 14, 1));
        $inventory->setItem(20, Item::get(160, 14, 1));
        $inventory->setItem(21, Item::get(160, 14, 1));
        $inventory->setItem(22, Item::get(160, 14, 1));
        $inventory->setItem(23, Item::get(160, 14, 1));
        $inventory->setItem(24, Item::get(160, 14, 1));
        $inventory->setItem(25, Item::get(160, 14, 1));
        $inventory->setItem(26, Item::get(160, 14, 1));
        $this->gui->send($player);
    }

    public function depositgui(Player $player)
    {
        $this->gui->readonly();
        $this->gui->setListener([$this, "banklistener"]);
        $this->gui->setName(TextFormat::GRAY . TextFormat::BOLD . "[" . TextFormat::AQUA . "Sans" . TextFormat::GOLD . "Bank" . TextFormat::GRAY . "]");
        $inventory = $this->gui->getInventory();
        $inventory->setItem(0, Item::get(160, 14, 1));
        $inventory->setItem(1, Item::get(160, 14, 1));
        $inventory->setItem(2, Item::get(160, 14, 1));
        $inventory->setItem(3, Item::get(160, 14, 1));
        $inventory->setItem(4, Item::get(160, 14, 1));
        $inventory->setItem(5, Item::get(160, 14, 1));
        $inventory->setItem(6, Item::get(160, 14, 1));
        $inventory->setItem(7, Item::get(160, 14, 1));
        $inventory->setItem(8, Item::get(160, 14, 1));
        $inventory->setItem(9, Item::get(160, 14, 1));
        $inventory->setItem(10, Item::get(266, 0, 64)->setCustomName(TextFormat::GRAY . "<------[" . TextFormat::GREEN . "Deposit 100" . TextFormat::GRAY . "]------>" . "\n\n" . TextFormat::YELLOW . "Klik Untuk memasukan semua uang ke bankmu"));
        $inventory->setItem(11, Item::get(160, 13, 1));
        $inventory->setItem(12, Item::get(160, 13, 1));
        $inventory->setItem(13, Item::get(266, 0, 32)->setCustomName(TextFormat::GRAY . "<------[" . TextFormat::GREEN . "Deposit 50" . TextFormat::GRAY . "]------>" . "\n\n" . TextFormat::YELLOW . "Klik untuk memasukan setengah uang ke bankmu"));
        $inventory->setItem(14, Item::get(160, 13, 1));
        $inventory->setItem(15, Item::get(160, 13, 1));
        $inventory->setItem(16, Item::get(266, 0, 1)->setCustomName(TextFormat::GRAY . "<------[" . TextFormat::GREEN . "Deposit" . TextFormat::GRAY . "]------>" . "\n\n" . TextFormat::YELLOW . "Klik untuk memasukan uang ke bankmu dengan nominal custom"));
        $inventory->setItem(17, Item::get(160, 14, 1));
        $inventory->setItem(18, Item::get(160, 14, 1));
        $inventory->setItem(19, Item::get(160, 14, 1));
        $inventory->setItem(20, Item::get(160, 14, 1));
        $inventory->setItem(21, Item::get(160, 14, 1));
        $inventory->setItem(22, Item::get(160, 14, 1));
        $inventory->setItem(23, Item::get(160, 14, 1));
        $inventory->setItem(24, Item::get(160, 14, 1));
        $inventory->setItem(25, Item::get(160, 14, 1));
        $inventory->setItem(26, Item::get(160, 14, 1));
        $this->gui->send($player);
    }

    public function banklistener(Player $player, Item $item)
    {
        if ($item->getId() == 266 and $item->getCustomName() == TextFormat::GRAY . "<------[" . TextFormat::GREEN . "Withdraw" . TextFormat::GRAY . "]------>" . "\n\n" . TextFormat::YELLOW . "Klik Untuk Penarikan Uang dari saldo bank mu") {
            $this->withdrawgui($player);
        }
        if ($item->getId() == 266 and $item->getCustomName() == TextFormat::GRAY . TextFormat::GRAY . "<------[" . TextFormat::GREEN . "Withdraw 100" . TextFormat::GRAY . "]------>" . "\n\n" . TextFormat::YELLOW . "Klik Untuk Menarik semua uang ke dompetmu") {
            $this->withdrawall($player);
        }
        if($item->getId() == 266 and $item->getCustomName() == TextFormat::GRAY . "<------[" . TextFormat::GREEN . "Withdraw 50" . TextFormat::GRAY . "]------>" . "\n\n" . TextFormat::YELLOW . "Klik untuk menarik setengah uang ke dompetmu"){
            $this->withdrawsetengah($player);
        }
        if($item->getId() == 266 and $item->getCustomName() == TextFormat::GRAY . "<------[" . TextFormat::GREEN . "Withdraw" . TextFormat::GRAY . "]------>" . "\n\n" . TextFormat::YELLOW . "Klik untuk menarik uang ke dompetmu dengan nominal custom"){
            $this->withdraw[$player->getLowerCaseName()] = true;
        }
        if ($item->getId() == 266 and $item->getCustomName() == TextFormat::GRAY . "<------[" . TextFormat::GREEN . "Deposit" . TextFormat::GRAY . "]------>" . "\n\n" . TextFormat::YELLOW . "Klik untuk memasukan uang ke saldo bankmu") {
            $this->depositgui($player);
        }
        if($item->getId() == 266 and $item->getCustomName() == TextFormat::GRAY . "<------[" . TextFormat::GREEN . "Deposit 100" . TextFormat::GRAY . "]------>" . "\n\n" . TextFormat::YELLOW . "Klik Untuk memasukan semua uang ke bankmu"){
            $this->depositall($player);
        }
        if($item->getId() == 266 and $item->getCustomName() == TextFormat::GRAY . "<------[" . TextFormat::GREEN . "Deposit 50" . TextFormat::GRAY . "]------>" . "\n\n" . TextFormat::YELLOW . "Klik untuk memasukan setengah uang ke bankmu"){
            $this->depositsetengah($player);
        }
        if($item->getId() == 266 and $item->getCustomName() == TextFormat::GRAY . "<------[" . TextFormat::GREEN . "Deposit" . TextFormat::GRAY . "]------>" . "\n\n" . TextFormat::YELLOW . "Klik untuk memasukan uang ke bankmu dengan nominal custom"){
            $this->deposit[$player->getLowerCaseName()] = true;
        }

    }

    public function getBalance(Player $player)
    {
        $playername = $player->getName();
        $balance = $this->getDatabase()->query("SELECT money FROM bank WHERE username='$playername'");
        $balance = mysqli_fetch_row($balance);
        return $balance[0];
    }

    public function setBalance(Player $player, int $value)
    {
        $playername = $player->getName();
        $this->getDatabase()->query("UPDATE bank SET money=$value WHERE username='$playername'");
    }

    public function addBalance(Player $player, int $value)
    {
        $playername = $player->getName();
        $balance = $this->getDatabase()->query("SELECT money FROM bank WHERE username='$playername'");
        $balance = mysqli_fetch_row($balance);
        $this->getDatabase()->query("UPDATE bank SET money=$balance[0] + $value WHERE username='$playername'");
    }

    public function withdrawall(Player $player)
    {
        $balance = $this->getBalance($player);
        if($balance !== 0) {
            EconomyAPI::getInstance()->addMoney($player, $balance);
            $this->setBalance($player, 0);
            $player->removeAllWindows();
        } else {
            $player->sendMessage("Uang anda tidak cukup");
        }
    }

    public function withdrawsetengah(Player $player)
    {
        $balance = $this->getBalance($player);
        if ($balance !== 0) {
            $balance = $balance / 2;
            EconomyAPI::getInstance()->addMoney($player, $balance);
            $this->setBalance($player, $balance);
            $player->removeAllWindows();
        } else {
            $player->sendMessage("Uang anda tidak cukup");
        }
    }

    public function withdrawcustom(Player $player, $value)
    {
        $balance = $this->getBalance($player);
        if ($balance !== 0 and $balance >= $value) {
            $balance = $balance - $value;
            EconomyAPI::getInstance()->addMoney($player, $value);
            $this->setBalance($player, $balance);
        } else {
            $player->sendMessage("Uang anda tidak cukup");
        }
    }

    public function depositall(Player $player){
        $balance = $this->getBalance($player);
        $money = EconomyAPI::getInstance()->myMoney($player);
        $this->addBalance($player, $money);
        EconomyAPI::getInstance()->setMoney($player, 0);
        $player->removeAllWindows();
    }

    public function depositsetengah(Player $player){
        $money = EconomyAPI::getInstance()->myMoney($player);
        $money = $money / 2;
        $this->addBalance($player, $money);
        EconomyAPI::getInstance()->setMoney($player, $money);
        $player->removeAllWindows();
    }

    public function depositcustom(Player $player, $value)
    {
        $money = EconomyAPI::getInstance()->myMoney($player);
        if ($money >= $value) {
            EconomyAPI::getInstance()->reduceMoney($player, $value);
            $this->addBalance($player, $value);
        } else {
            $player->sendMessage("Uang anda tidak cukup");
        }
    }
}