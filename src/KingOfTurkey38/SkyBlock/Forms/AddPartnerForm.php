<?php

declare(strict_types=1);

namespace KingOfTurkey38\SkyBlock\Forms;

use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\SimpleForm;
use KingOfTurkey38\SkyBlock\IslandManager;
use KingOfTurkey38\SkyBlock\Main;
use KingOfTurkey38\SkyBlock\Utils\Messages;
use pocketmine\Player;

class AddPartnerForm extends CustomForm implements Messages {

    const TYPE = "add_partner_form";

    private $plugin;

    private $players = [];

    public function __construct(Player $player, Main $plugin)
    {
        parent::__construct([$this, "onSubmit"]);
        $this->plugin = $plugin;

        $data = $plugin->getConfigData();
        $form = $data["forms"][self::TYPE];
        $main = Main::getInstance();

        $partners = $main->getSession($player)->getIslandPartners();

        if(sizeof($partners) >= $data["settings"]["max_amount_of_partners"]){
            $player->sendMessage($data["messages"][self::MAX_PARTNERS_REACHED]);
            return;
        }

        $online = $main->getServer()->getOnlinePlayers();
        $dropdown = [];
        foreach($online as $key => $value){
            if(in_array($value->getName(), $partners) || $value->getName() === $player->getName()){
                continue;
            }

            $dropdown[] = $value->getName();
        }

        $this->players = $dropdown;

        $this->setTitle($form["title"]);
        $this->addLabel($form["label"]);
        $this->addDropdown($form["dropdown_text"], $dropdown);

        $player->sendForm($this);
    }

    public function onSubmit(Player $player, ?array $data): void
    {
        $messages = $this->plugin->getConfigData()["messages"];
        if($data !== null){
            $p = $data[1];
            if(isset($this->players[$p])){
                $name = $this->players[$p];
                $partner = $this->plugin->getServer()->getPlayer($name);
                $session = $this->plugin->getSession($player);
                if($partner){
                    $session->addIslandPartner($partner);
                    $player->sendMessage($messages[self::ISLAND_PARTNER_ADDED]);
                }
            }
        }
    }
}