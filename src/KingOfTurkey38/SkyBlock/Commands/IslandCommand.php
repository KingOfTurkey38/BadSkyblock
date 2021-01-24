<?php

declare(strict_types=1);

namespace KingOfTurkey38\SkyBlock\Commands;

use KingOfTurkey38\SkyBlock\Forms\DefaultIslandForm;
use KingOfTurkey38\SkyBlock\Forms\IslandLeaderForm;
use KingOfTurkey38\SkyBlock\Main;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;

class IslandCommand extends PluginCommand{

    /** @var Main */
    private $plugin;

    public function __construct(Main $plugin)
    {
        parent::__construct("ada", $plugin);
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if($sender instanceof Player){
            $session = $this->plugin->getSession($sender);
            if(!$session->getIslandName()){
                new DefaultIslandForm($sender, $this->plugin);
            } else {
                new IslandLeaderForm($sender, $this->plugin);
            }
        }
    }
}