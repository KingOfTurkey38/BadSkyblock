<?php


declare(strict_types=1);

namespace KingOfTurkey38\SkyBlock\Forms;

use jojoe77777\FormAPI\CustomForm;
use KingOfTurkey38\SkyBlock\Main;
use KingOfTurkey38\SkyBlock\Tasks\IslandVisitTask;
use KingOfTurkey38\SkyBlock\Utils\Messages;
use pocketmine\level\Position;
use pocketmine\Player;

class IslandVisitForm extends CustomForm implements Messages {

    const TYPE = "island_visit_form";

    private $plugin;

    public function __construct(Player $player, Main $plugin)
    {
        parent::__construct([$this, "onSubmit"]);
        $this->plugin = $plugin;

        $data = $plugin->getConfigData();
        $form = $data["forms"][self::TYPE];

        $this->setTitle($form["title"]);
        $this->addLabel($form["label"]);
        $this->addInput($form["input_text"]);

        $player->sendForm($this);
    }

    public function onSubmit(Player $player, ?array $data): void
    {
        $messages = $this->plugin->getConfigData()["messages"];
        if($data){
            $island = strtolower($data[1]);
            if($island){
                $p = $this->plugin->getServer()->getPlayer($island);
                if($p){
                    $session = $this->plugin->getSession($p);
                    if($session->getIslandName()){
                        $warp = $session->getIslandWarp();
                        if($warp){
                            $this->plugin->getServer()->loadLevel(strtolower($session->getIslandName()));
                            $level = $this->plugin->getServer()->getLevelByName(strtolower($session->getIslandName()));
                            $player->teleport(Position::fromObject($warp, $level));
                            return;
                        } else {
                            $player->sendMessage($messages[Messages::ISLAND_WARP_NOT_SET]);
                            return;
                        }
                    } else {
                        $player->sendMessage($messages[self::PLAYER_HAS_NO_ISLAND]);
                        return;
                    }
                }

                if(isset($this->plugin->cachedData[$island])){
                    $data = $this->plugin->cachedData[$island];
                    if($data["data"]["warp"]){
                        $this->plugin->getServer()->loadLevel(strtolower($island));
                        $level = $this->plugin->getServer()->getLevelByName(strtolower($island));
                        $player->teleport(new Position($data["data"]["warp"]["x"], $data["data"]["warp"]["y"], $data["data"]["warp"]["z"], $level));
                        return;
                    } else {
                        $player->sendMessage($messages[Messages::ISLAND_WARP_NOT_SET]);
                        return;
                    }
                }

                $this->plugin->getServer()->getAsyncPool()->submitTask(new IslandVisitTask($player->getName(), $island));
            }
        }
    }
}