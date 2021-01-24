<?php

declare(strict_types=1);

namespace KingOfTurkey38\SkyBlock\Forms;

use jojoe77777\FormAPI\CustomForm;
use KingOfTurkey38\SkyBlock\Main;
use KingOfTurkey38\SkyBlock\Utils\Messages;
use pocketmine\Player;

class AcceptPartnerForm extends CustomForm implements Messages {

    const TYPE = "partner_accept_form";

    private $plugin;

    private $players = [];

    public function __construct(Player $player, Main $plugin)
    {
        parent::__construct([$this, "onSubmit"]);
        $this->plugin = $plugin;

        $data = $plugin->getConfigData();
        $form = $data["forms"][self::TYPE];
        $main = Main::getInstance();

        $partners = $main->getSession($player)->getPartners();

        $this->setTitle($form["title"]);
        $this->addLabel($form["label"]);
        $this->addDropdown($form["dropdown_text"], $partners);

        $player->sendForm($this);
    }

    public function onSubmit(Player $player, ?array $data): void
    {
        $messages = $this->plugin->getConfigData()["messages"];
        if($data !== null){
            $main = Main::getInstance();
            $partners = $main->getSession($player)->getPartners();
            $p = $data[1];
            if(isset($partners[$p])){
                $pl = $main->getServer()->getPlayer($partners[$p]);
                if($pl){
                    $se = $main->getSession($pl);
                }
            }
        }
    }
}