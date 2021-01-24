<?php


declare(strict_types=1);

namespace KingOfTurkey38\SkyBlock\Forms;

use jojoe77777\FormAPI\SimpleForm;
use KingOfTurkey38\SkyBlock\IslandManager;
use KingOfTurkey38\SkyBlock\Main;
use KingOfTurkey38\SkyBlock\Utils\Messages;
use pocketmine\Player;

class DefaultIslandForm extends SimpleForm implements Messages {

    const TYPE = "non_island_form";

    private $plugin;

    public function __construct(Player $player, Main $plugin)
    {
        parent::__construct([$this, "onSubmit"]);
        $this->plugin = $plugin;

        $data = $plugin->getConfigData();
        $form = $data["forms"][self::TYPE];

        $this->setTitle($form["title"]);
        $this->addButton($form["buttons"]["island_create_button"]);
        $this->addButton($form["buttons"]["island_visit_button"]);

        $player->sendForm($this);
    }

    public function onSubmit(Player $player, ?int $data): void
    {
        $messages = $this->plugin->getConfigData()["messages"];
        if($data !== null){
            switch ($data){
                case 0:
                    IslandManager::createIsland($player);
                    $player->sendMessage($messages[self::ISLAND_SUCCESSFULLY_CREATED]);
                    break;
                case 1:
                    new IslandVisitForm($player, $this->plugin);
                    break;
            }
        }
    }
}