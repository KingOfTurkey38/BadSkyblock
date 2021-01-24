<?php

declare(strict_types=1);

namespace KingOfTurkey38\SkyBlock;

use KingOfTurkey38\SkyBlock\Tasks\MySQLQueryTask;
use mysqli_result;
use pocketmine\math\Vector3;
use pocketmine\Player;

class Session {

    /** @var Player */
    private $player;
    /** @var array  */
    private $playerData = [];

    /** @var string */
    private $island;
    /** @var array */
    private $partner;
    /** @var array */
    private $islandData;

    public function __construct(Player $player, array $playerData, ?array $islandData)
    {
        $this->player = $player;
        $this->playerData = $playerData;
        $this->islandData = $islandData;

        $this->island = $this->playerData["island"];
        $this->partner = json_decode($this->playerData["partner"], true);

        if($this->islandData){
            $this->islandData = json_decode($islandData["data"], true);
            $this->islandData["home"] = new Vector3($this->islandData["home"]["x"], $this->islandData["home"]["y"], $this->islandData["home"]["z"]);
            if($this->islandData["warp"]){
                $this->islandData["warp"] = new Vector3($this->islandData["warp"]["x"], $this->islandData["warp"]["y"], $this->islandData["warp"]["z"]);
            }
            $main = Main::getInstance();
            if(isset($main->cachedData[strtolower(strtolower($this->getIslandName()))])){
                unset($main->cachedData[strtolower($this->getIslandName())]);
            }
        }
    }

    /**
     * @return Player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function saveSession(): void
    {
        $islandName = $this->getIslandName();
        $username = $this->getPlayer()->getName();
        $queries = ["UPDATE Users SET island='$islandName' WHERE username='$username'"];

        if($this->islandData){
            $data = json_encode($this->islandData);
            $queries[] = "INSERT INTO Islands (island, data) VALUES ('$islandName', '$data')";
            $queries[] = "UPDATE Islands SET data=:'$data' WHERE island='$islandName'";
        }
        Main::getInstance()->getServer()->getAsyncPool()->submitTask(new MySQLQueryTask($queries));
    }

    /**
     * @param array $islandData
     */
    public function setIslandData(?array $islandData): void
    {
        $this->islandData = $islandData;
    }

    public function getIslandHome(): ?Vector3
    {
        return $this->islandData ? $this->islandData["home"] : null;
    }

    public function getIslandWarp(): ?Vector3
    {
        return $this->islandData ? $this->islandData["warp"] : null;
    }

    public function setIslandWarp(?Vector3 $vector3){
        $this->islandData["warp"] = $vector3;
    }

    /**
     * @return array
     */
    public function getIslandData(): array
    {
        return $this->islandData;
    }

    public function setIslandHome(Vector3 $vector3): void
    {
        if($this->islandData){
            $this->islandData["home"] = $vector3;
        }
    }

    public function addPartner(Player $player): void
    {
        $this->partner[] = $player->getName();
    }

    public function removePartner(int $index): void
    {
        unset($this->partner[$index]);
    }

    public function getPartners(): array
    {
        return $this->partner;
    }

    public function addIslandPartner(Player $player): void
    {
        $this->islandData["partners"][] = $player->getName();
    }

    public function removeIslandPartner(int $index): void
    {
        var_dump($this->islandData);
        unset($this->islandData["partners"][$index]);
    }

    public function getIslandPartners(): ?array
    {
        return $this->islandData ? $this->islandData["partners"] : null;
    }

    public function setIslandName(?string $island): void
    {
        $this->island = $island;
    }

    public function getIslandName(): ?string
    {
        return $this->island;
    }

    /**
     * @return array
     */
    public function getPlayerData(): array
    {
        return $this->playerData;
    }
}