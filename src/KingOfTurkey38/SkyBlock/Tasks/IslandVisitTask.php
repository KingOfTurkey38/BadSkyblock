<?php

declare(strict_types=1);

namespace KingOfTurkey38\SkyBlock\Tasks;

use KingOfTurkey38\SkyBlock\Main;
use KingOfTurkey38\SkyBlock\Utils\Messages;
use pocketmine\level\Position;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

class IslandVisitTask extends AsyncTask {

    private $player;

    private $island;

    public function __construct(string $player, string $island)
    {
        $this->player = $player;
        $this->island = $island;
    }

    public function onRun()
    {
        $con = mysqli_connect("eu.sql.titannodes.com", "u6227_yE8aiBRo9K", "KIL=v@7z40nW+rhSucDKinDn", "s6227_turkey", 3306);
        $query = $con->query("SELECT * FROM Islands WHERE island='$this->island'");
        $this->setResult($query->fetch_array());
    }

    public function onCompletion(Server $server)
    {
        $player = $server->getPlayer($this->player);
        if(!$player){
            return;
        }

        /** @var Main $plugin */
        $plugin = $server->getPluginManager()->getPlugin("SkyBlock");
        $messages = $plugin->getConfigData()["messages"];

        $data = $this->getResult();
        if(empty($data)){
            $player->sendMessage($messages[Messages::ISLAND_NOT_FOUND]);
            return;
        }

        $data["data"] = json_decode($data["data"], true);

        $plugin->cachedData[strtolower($data["island"])] = $data;

        if($data["data"]["warp"]){
            $server->loadLevel(strtolower($data["island"]));
            $level = $server->getLevelByName(strtolower($data["island"]));
            $player->teleport(new Position($data["data"]["warp"]["x"], $data["data"]["warp"]["y"], $data["data"]["warp"]["z"], $level));
        } else {
            $player->sendMessage($messages[Messages::ISLAND_WARP_NOT_SET]);
            return;
        }


    }
}