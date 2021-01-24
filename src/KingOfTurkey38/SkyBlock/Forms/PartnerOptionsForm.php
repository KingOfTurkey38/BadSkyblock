<?php


declare(strict_types=1);

namespace KingOfTurkey38\SkyBlock\Forms;

use jojoe77777\FormAPI\CustomForm;
use KingOfTurkey38\SkyBlock\Main;
use KingOfTurkey38\SkyBlock\Utils\IslandOptions;
use KingOfTurkey38\SkyBlock\Utils\Messages;
use pocketmine\Player;

class PartnerOptionsForm extends CustomForm implements Messages {

    const TYPE = "partner_options_form";

    private $plugin;

    public function __construct(Player $player, Main $plugin)
    {
        parent::__construct([$this, "onSubmit"]);
        $this->plugin = $plugin;

        $data = $plugin->getConfigData();
        $form = $data["forms"][self::TYPE];
        $main = Main::getInstance();
        $session = $main->getSession($player);
        $data = $session->getIslandData();

        $this->setTitle($form["title"]);
        $this->addLabel($form["label"]);
        $this->addToggle($form["block_break_option"], $data["options"][IslandOptions::BREAK_BLOCK]);
        $this->addToggle($form["block_place_option"], $data["options"][IslandOptions::PLACE_BLOCK]);
        $this->addToggle($form["open_chest_option"], $data["options"][IslandOptions::OPEN_CHEST]);
        $this->addToggle($form["pick_up_item_option"], $data["options"][IslandOptions::PICK_ITEM]);

        $player->sendForm($this);
    }

    public function onSubmit(Player $player, ?array $data): void
    {
        $messages = $this->plugin->getConfigData()["messages"];
        if($data){
            $options = [];
            $options[IslandOptions::BREAK_BLOCK] = $data[1];
            $options[IslandOptions::PLACE_BLOCK] = $data[2];
            $options[IslandOptions::OPEN_CHEST] = $data[3];
            $options[IslandOptions::PICK_ITEM] = $data[4];

            $session = $this->plugin->getSession($player);
            $data = $session->getIslandData();
            $data["options"] = $options;
            $session->setIslandData($data);
        }
    }
}