<?php


declare(strict_types=1);

namespace KingOfTurkey38\SkyBlock\Forms;

use czechpmdevs\multiworld\generator\skyblock\populator\Island;
use jojoe77777\FormAPI\SimpleForm;
use KingOfTurkey38\SkyBlock\IslandManager;
use KingOfTurkey38\SkyBlock\Main;
use KingOfTurkey38\SkyBlock\Utils\Messages;
use pocketmine\Player;

class IslandLeaderForm extends SimpleForm implements Messages {

    const TYPE = "island_form";

    private $plugin;

    public function __construct(Player $player, Main $plugin)
    {
        parent::__construct([$this, "onSubmit"]);
        $this->plugin = $plugin;

        $data = $plugin->getConfigData();
        $form = $data["forms"][self::TYPE];

        $this->setTitle($form["title"]);
        $this->addButton($form["buttons"]["island_home_button"]);
        $this->addButton($form["buttons"]["island_set_home_button"]);
        $this->addButton($form["buttons"]["island_set_warp_button"]);
        $this->addButton($form["buttons"]["island_unset_warp_button"]);
        $this->addButton($form["buttons"]["island_visit_button"]);
        $this->addButton($form["buttons"]["island_add_partner_button"]);
        $this->addButton($form["buttons"]["island_remove_partner_button"]);
        $this->addButton($form["buttons"]["island_partner_options_button"]);
        $this->addButton($form["buttons"]["island_disband_button"]);

        $player->sendForm($this);
    }

    public function onSubmit(Player $player, ?int $data): void
    {
        $messages = $this->plugin->getConfigData()["messages"];
        $session = $this->plugin->getSession($player);
        if($data !== null){
            switch ($data){
                case 0:
                    IslandManager::teleportHome($player);
                    break;
                case 1:
                    IslandManager::setHome($player); // no vector3 param bc it sets it to the players position
                    $player->sendMessage($messages[self::ISLAND_HOME_SUCCESSFULLY_UPDATED]);
                    break;
                case 2:
                    $session->setIslandWarp($player->asVector3());
                    $player->sendMessage($messages[self::ISLAND_WARP_SET]);
                    break;
                case 3:
                    $session->setIslandWarp(null);
                    $player->sendMessage($messages[self::ISLAND_WARP_UNSET]);
                    break;
                case 4:
                    new IslandVisitForm($player, $this->plugin);
                    break;
                case 5:
                    new AddPartnerForm($player, $this->plugin);
                    break;
                case 6:
                    new RemovePartnerForm($player, $this->plugin);
                    break;
                case 7:
                    new PartnerOptionsForm($player, $this->plugin);
                    break;
                case 8:
                    IslandManager::disbandIsland($player);
                    $player->sendMessage($messages[self::ISLAND_DISBAND]);
                    break;
            }
        }
    }
}