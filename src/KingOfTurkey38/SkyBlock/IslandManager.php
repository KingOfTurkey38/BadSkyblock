<?php

declare(strict_types=1);

namespace KingOfTurkey38\SkyBlock;

use KingOfTurkey38\SkyBlock\Generators\IslandGenerator;
use KingOfTurkey38\SkyBlock\Tasks\MySQLQueryTask;
use KingOfTurkey38\SkyBlock\Utils\IslandDataUtils;
use pocketmine\item\Item;
use pocketmine\level\generator\GeneratorManager;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\tile\Chest;
use pocketmine\tile\Tile;

class IslandManager {

    public static function createIsland(Player $player): void{
        $main = Main::getInstance();
        $gen = GeneratorManager::getGenerator("island");
        $levelName = strtolower($player->getName());
        $main->getServer()->generateLevel($levelName, null, $gen);
        $main->getServer()->loadLevel($levelName);
        $level = $main->getServer()->getLevelByName($levelName);

        $level->loadChunk(IslandGenerator::getChestPosition()->getX(), IslandGenerator::getChestPosition()->getZ());

        $level->setSpawnLocation(IslandGenerator::getWorldSpawn());

        $session = $main->getSession($player);

        $session->setIslandName($player->getName());
        $session->setIslandData(IslandDataUtils::createIslandData($player));

        $player->teleport(new Position(IslandGenerator::getChestPosition()->getX(), IslandGenerator::getChestPosition()->getY(), IslandGenerator::getChestPosition()->getZ(), $level));

        $chest = Tile::createTile(Tile::CHEST, $level, new CompoundTag(" ", [
            new ListTag("Items", []),
            new StringTag("id", Tile::CHEST),
            new IntTag("x", IslandGenerator::getChestPosition()->getX()),
            new IntTag("y", IslandGenerator::getChestPosition()->getY()),
            new IntTag("z", IslandGenerator::getChestPosition()->getZ())
        ]));
        $level->addTile($chest);

        if($chest instanceof Chest) {
            $items = [Item::get(Item::ICE, 0, 2), Item::get(Item::MELON), Item::get(Item::TORCH, 0, 2), Item::get(Item::BONE), Item::get(Item::BUCKET, 10), Item::get(Item::PUMPKIN_SEEDS), Item::get(Item::SUGARCANE), Item::get(Item::RED_MUSHROOM), Item::get(Item::BROWN_MUSHROOM), Item::get(Item::CACTUS), Item::get(Item::BREAD), Item::get(Item::MELON_SEEDS), Item::get(Item::WHEAT_SEEDS), Item::get(Item::SAPLING), Item::get(Item::LEATHER_CAP), Item::get(Item::LEATHER_CHESTPLATE), Item::get(Item::LEATHER_LEGGINGS), Item::get(Item::LEATHER_BOOTS)];
            foreach ($items as $item) {
                $chest->getInventory()->addItem($item);
            }
        }
    }

    public static function teleportHome(Player $player): void
    {
        $main = Main::getInstance();
        $session = $main->getSession($player);

        $levelName = strtolower($session->getIslandName());

        if(!$main->getServer()->isLevelLoaded($levelName)){
            $main->getServer()->loadLevel($levelName);
        }

        $level = $main->getServer()->getLevelByName($levelName);
        $spawnPoint = $session->getIslandHome();

        $player->teleport(new Position($spawnPoint->getX(), $spawnPoint->getY(), $spawnPoint->getZ(), $level));
    }

    public static function setHome(Player $player): void
    {
        $main = Main::getInstance();
        $session = $main->getSession($player);
        $island = strtolower($session->getIslandName());

        $main->getServer()->loadLevel($island);
        $level = $main->getServer()->getLevelByName($island);
        $level->setSpawnLocation($player->asVector3());

        $session->setIslandHome($player->asVector3());
    }

    public static function disbandIsland(Player $player): void
    {
        $main = Main::getInstance();
        $session = $main->getSession($player);
        if($session){
            $island = strtolower($session->getIslandName());
            $main->getServer()->loadLevel($island);
            $level = $main->getServer()->getLevelByName($island);
            if($level){
                self::deleteLevel($level);
                $session->setIslandData(null);
                $session->setIslandName(null);
                $main->getServer()->getAsyncPool()->submitTask(new MySQLQueryTask(["DELETE FROM Islands WHERE LOWER(island)='$island'"]));
            }
        }
    }

    /**
     * @param Level $level
     */
    public static function deleteLevel(Level $level): void
    {
        $path = $level->getProvider()->getPath();
        Main::getInstance()->getServer()->unloadLevel($level, true);
        self::deleteDir($path);
    }

    /**
     * @param string $dirPath
     * @return int
     */
    private static function deleteDir(string $dirPath): int
    {
        $files = 1;
        if (basename($dirPath) == "." || basename($dirPath) == "..") {
            return 0;
        }
        foreach (scandir($dirPath) as $item) {
            if ($item != "." || $item != "..") {
                if (is_dir($dirPath . DIRECTORY_SEPARATOR . $item)) {
                    $files += self::deleteDir($dirPath . DIRECTORY_SEPARATOR . $item);
                }

                if (is_file($dirPath . DIRECTORY_SEPARATOR . $item)) {
                    $files += self::removeFile($dirPath . DIRECTORY_SEPARATOR . $item);
                }
            }
        }
        rmdir($dirPath);
        return $files;
    }

    /**
     * @param string $path
     * @return int
     */
    private static function removeFile(string $path): int
    {
        unlink($path);
        return 1;
    }
}