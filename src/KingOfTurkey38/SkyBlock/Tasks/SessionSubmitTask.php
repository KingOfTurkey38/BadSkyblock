<?php

declare(strict_types=1);

namespace KingOfTurkey38\SkyBlock\Tasks;

use KingOfTurkey38\SkyBlock\Main;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

class SessionSubmitTask extends AsyncTask {

    private $player;

    private $query;

    public function __construct(string $player)
    {
        $this->player = $player;
    }

    public function onRun()
    {
        $con = mysqli_connect("", "", "K", "", 3306);
        $query = $con->query("SELECT * FROM Users WHERE username='$this->player'");
        $query2 = null;
        $result1 = $query->fetch_array();
        if($result1["island"]){
            $island = $result1["island"];
            $query2 = $con->query("SELECT * FROM Islands WHERE island='$island'")->fetch_array();
        }
        $this->setResult([$result1, $query2]);
    }

    public function onCompletion(Server $server)
    {
        $player = $server->getPlayer($this->player);
        if(!$player){
            return;
        }
        /** @var Main $plugin */
        $plugin = $server->getPluginManager()->getPlugin("SkyBlock");
        $plugin->addSession($player, $this->getResult());
    }
}