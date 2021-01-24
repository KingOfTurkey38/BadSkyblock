<?php

declare(strict_types=1);

namespace KingOfTurkey38\SkyBlock\Tasks;

use KingOfTurkey38\SkyBlock\Main;
use KingOfTurkey38\SkyBlock\Utils\Messages;
use pocketmine\level\Position;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

class CacheIslandDataTask extends AsyncTask {

    private $player;

    private $island;

    public function __construct(string $island)
    {
        $this->island = $island;
    }

    public function onRun()
    {
        $con = mysqli_connect("", "", "K", "", 3306);
        $query = $con->query("SELECT * FROM Islands WHERE island='$this->island'");
        $this->setResult($query->fetch_array());
    }

    public function onCompletion(Server $server)
    {
        $result = $this->getResult();
        if(!empty($result)){
            /** @var Main $plugin */
            $plugin = $server->getPluginManager()->getPlugin("SkyBlock");
            $result["data"] = json_decode($result["data"], true);
            $plugin->cachedData[strtolower($result["island"])] = $result;
        }
    }
}