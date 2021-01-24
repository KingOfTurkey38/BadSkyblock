<?php

declare(strict_types=1);

namespace KingOfTurkey38\SkyBlock\Listeners;

use KingOfTurkey38\SkyBlock\Main;
use KingOfTurkey38\SkyBlock\Tasks\MySQLQueryTask;
use KingOfTurkey38\SkyBlock\Tasks\SessionSubmitTask;
use KingOfTurkey38\SkyBlock\Utils\IslandOptions;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\inventory\InventoryPickupItemEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;

class EventListener implements Listener{

    /** @var Main */
    private $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onJoin(PlayerJoinEvent $event): void
    {
        $player = $event->getPlayer();
        $uuid = $player->getUniqueId()->toString();
        $username = $player->getName();
        $this->plugin->getServer()->getAsyncPool()->submitTask(new MySQLQueryTask(["INSERT INTO Users (uuid, username) VALUES ('$uuid', '$username')"]));
        $this->plugin->getServer()->getAsyncPool()->submitTask(new SessionSubmitTask($username));
    }

    /**
     * @param BlockBreakEvent $event
     * @priority HIGH
     * @ignoreCancelled true
     */
    public function onBreak(BlockBreakEvent $event): void
    {
        $player = $event->getPlayer();
        $level = $player->getLevel();
        if(strtolower($player->getName()) !== strtolower($level->getFolderName())){
            $levelName = strtolower($level->getFolderName());
            $p = $this->plugin->getServer()->getPlayer($levelName);
            if($p){
                $session = $this->plugin->getSession($p);
                if($session->getIslandData()){
                    $data = $session->getIslandData();
                    if(!in_array($player->getName(), $data["partners"])){
                        $event->setCancelled(true);
                        return;
                    }
                    if(!$data["options"][IslandOptions::BREAK_BLOCK]){
                        $event->setCancelled(true);
                    }
                }
            } elseif (isset($this->plugin->cachedData[$levelName])){
                $data = $this->plugin->cachedData[$levelName]["data"];
                if(!in_array($player->getName(), $data["partners"])) {
                    $event->setCancelled(true);
                    return;
                }
                if(!$data[IslandOptions::BREAK_BLOCK]){
                    $event->setCancelled(true);
                }
            }
        }
    }

    /**
     * @param BlockPlaceEvent $event
     * @priority HIGH
     * @ignoreCancelled true
     */
    public function onPlace(BlockPlaceEvent $event): void
    {
        $player = $event->getPlayer();
        $level = $player->getLevel();
        if(strtolower($player->getName()) !== strtolower($level->getFolderName())){
            $levelName = strtolower($level->getFolderName());
            $p = $this->plugin->getServer()->getPlayer($levelName);
            if($p){
                $session = $this->plugin->getSession($p);
                if($session->getIslandData()){
                    $data = $session->getIslandData();
                    if(!in_array($player->getName(), $data["partners"])){
                        $event->setCancelled(true);
                        return;
                    }
                    if(!$data["options"][IslandOptions::PLACE_BLOCK]){
                        $event->setCancelled(true);
                    }
                }
            } elseif (isset($this->plugin->cachedData[$levelName])){
                $data = $this->plugin->cachedData[$levelName]["data"];
                if(!in_array($player->getName(), $data["partners"])) {
                    $event->setCancelled(true);
                    return;
                }
                if(!$data[IslandOptions::PLACE_BLOCK]){
                    $event->setCancelled(true);
                }
            }
        }
    }

    /**
     * @param PlayerInteractEvent $event
     * @priority HIGH
     * @ignoreCancelled true
     */
    public function onInteract(PlayerInteractEvent $event): void
    {
        $player = $event->getPlayer();
        $level = $player->getLevel();
        if(strtolower($player->getName()) !== strtolower($level->getName())){
            $levelName = strtolower($level->getFolderName());
            $p = $this->plugin->getServer()->getPlayer($levelName);
            if($p){
                $session = $this->plugin->getSession($p);
                if($session->getIslandData()){
                    $data = $session->getIslandData();
                    if(!in_array($player->getName(), $data["partners"])){
                        $event->setCancelled(true);
                        return;
                    }
                    if(!$data["options"][IslandOptions::OPEN_CHEST]){
                        $event->setCancelled(true);
                    }
                }
            } elseif (isset($this->plugin->cachedData[$levelName])){
                $data = $this->plugin->cachedData[$levelName]["data"];
                if(!in_array($player->getName(), $data["partners"])) {
                    $event->setCancelled(true);
                    return;
                }
                if(!$data[IslandOptions::OPEN_CHEST]){
                    $event->setCancelled(true);
                }
            }
        }
    }

    /**
     * @param InventoryPickupItemEvent $event
     * @priority HIGH
     * @ignoreCancelled true
     */
    public function onPickUp(InventoryPickupItemEvent $event): void
    {
        $player = null;
        foreach ($event->getViewers() as $viewer) {
            $player = $viewer;
            break;
        }

        if(!$player){
            return;
        }
        $level = $player->getLevel();
        if(strtolower($player->getName()) !== strtolower($level->getFolderName())){
            $levelName = strtolower($level->getFolderName());
            $p = $this->plugin->getServer()->getPlayer($levelName);
            if($p){
                $session = $this->plugin->getSession($p);
                if($session->getIslandData()){
                    $data = $session->getIslandData();
                    if(!in_array($player->getName(), $data["partners"])){
                        $event->setCancelled(true);
                        return;
                    }
                    if(!$data["options"][IslandOptions::PICK_ITEM]){
                        $event->setCancelled(true);
                    }
                }
            } elseif (isset($this->plugin->cachedData[$levelName])){
                $data = $this->plugin->cachedData[$levelName]["data"];
                if(!in_array($player->getName(), $data["partners"])){
                    $event->setCancelled(true);
                    return;
                }
                if(!in_array($player->getName(), $data["partners"])) {
                    $event->setCancelled(true);
                    return;
                }
                if(!$data[IslandOptions::PICK_ITEM]){
                    $event->setCancelled(true);
                }
            }
        }
    }

    public function onQuit(PlayerQuitEvent $event): void
    {
        $player = $event->getPlayer();
        $session = $this->plugin->getSession($player);

        if($session){
            $level = $this->plugin->getServer()->getLevelByName(strtolower($session->getIslandName()));
            if($level) {
                $this->plugin->getServer()->unloadLevel($level);
            }
        }

        $this->plugin->removeSession($player);
    }
}