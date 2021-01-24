<?php

declare(strict_types=1);

namespace KingOfTurkey38\SkyBlock\Utils;

use KingOfTurkey38\SkyBlock\Generators\IslandGenerator;
use pocketmine\Player;

class IslandDataUtils implements IslandOptions {

    public static function createIslandData(Player $player): array
    {
        $data = [];
        $data["leader"] = $player->getName();
        $data["partners"] = [];
        $data["warp"] = null;
        $data["home"] = IslandGenerator::getWorldSpawn();
        $data["level"] = 0;
        $data["options"] = [self::BREAK_BLOCK => true, self::PLACE_BLOCK => true, self::OPEN_CHEST => true, self::PICK_ITEM => true];

        return $data;
    }
}