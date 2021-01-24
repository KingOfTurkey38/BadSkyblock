<?php


declare(strict_types=1);

namespace KingOfTurkey38\SkyBlock\Forms;

use jojoe77777\FormAPI\CustomForm;
use KingOfTurkey38\SkyBlock\IslandManager;
use KingOfTurkey38\SkyBlock\Main;
use KingOfTurkey38\SkyBlock\Utils\Messages;
use pocketmine\Player;

class RemovePartnerForm extends CustomForm implements Messages {

    const TYPE = "remove_partner_form";

    private $plugin;

    public function __construct(Player $player, Main $plugin)
    {
        parent::__construct([$this, "onSubmit"]);
        $this->plugin = $plugin;

        $data = $plugin->getConfigData();
        $form = $data["forms"][self::TYPE];
        $main = Main::getInstance();

        $partners = $main->getSession($player)->getIslandPartners();

        $this->setTitle($form["title"]);
        $this->addLabel($form["label"]);
        $this->addDropdown($form["dropdown_text"], $partners, 0);

        $player->sendForm($this);
    }

    public function onSubmit(Player $player, ?array $data): void
    {
        $messages = $this->plugin->getConfigData()["messages"];
        if($data){
            $p = $data[1];
            $session = Main::getInstance()->getSession($player);
            $session->removeIslandPartner($p);
            $player->sendMessage($messages[self::ISLAND_PARTNER_REMOVED]);
        }
    }
}