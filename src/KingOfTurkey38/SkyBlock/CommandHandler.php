<?php

declare(strict_types=1);

namespace KingOfTurkey38\SkyBlock;

use KingOfTurkey38\SkyBlock\Commands\IslandCommand;

class CommandHandler {

    /** @var Main */
    private $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;

        $this->initCommands();
    }

    public function initCommands(): void
    {
        $this->plugin->getServer()->getCommandMap()->register("SkyBlock", new IslandCommand($this->plugin));
    }
}