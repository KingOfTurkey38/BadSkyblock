<?php

declare(strict_types=1);

namespace KingOfTurkey38\SkyBlock;

use KingOfTurkey38\SkyBlock\Generators\IslandGenerator;
use KingOfTurkey38\SkyBlock\Listeners\EventListener;
use KingOfTurkey38\SkyBlock\Tasks\MySQLQueryTask;
use pocketmine\level\generator\GeneratorManager;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase{

    /** @var Session[] */
    private $sessions = [];
    /** @var Main */
    private static $instance;

    private $configData;
    /** @var array  */
    public $cachedData = [];

    public function onEnable()
    {
        self::$instance = $this;

        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->initDatabase();
        $this->configData = $this->getConfig()->getAll();

        GeneratorManager::addGenerator(IslandGenerator::class, "island", true);

        new CommandHandler($this);
    }

    public function onDisable()
    {
        if(!empty($this->sessions)){
            foreach ($this->sessions as $session){
                $session->saveSession();
            }
        }
    }

    public function addSession(Player $player, array $data): void
    {
        if(!isset($this->sessions[$player->getName()])){
            $this->sessions[$player->getName()] = new Session($player, $data[0], $data[1]);
        }
    }

    public function removeSession(Player $player): void
    {
        if(isset($this->sessions[$player->getName()])){
            $this->sessions[$player->getName()]->saveSession();
            unset($this->sessions[$player->getName()]);
        }
    }

    public function getSession(Player $player): ?Session
    {
        return isset($this->sessions[$player->getName()]) ? $this->sessions[$player->getName()] : null;
    }

    public function initDatabase(): void
    {
        $queries = ["CREATE TABLE IF NOT EXISTS Users (uuid VARCHAR(50), username VARCHAR(50), island VARCHAR(50) DEFAULT null, partner BLOB DEFAULT '{}', PRIMARY KEY (uuid));", "CREATE TABLE IF NOT EXISTS Islands (island VARCHAR(50), data BLOB, PRIMARY KEY (island))"];
        $this->getServer()->getAsyncPool()->submitTask(new MySQLQueryTask($queries));
    }

    public function getConfigData(): array
    {
        return $this->configData;
    }

    /**
     * @return Main
     */
    public static function getInstance(): Main
    {
        return self::$instance;
    }

}
