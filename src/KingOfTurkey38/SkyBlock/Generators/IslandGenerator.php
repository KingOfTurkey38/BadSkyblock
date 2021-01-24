<?php

declare(strict_types=1);

namespace KingOfTurkey38\SkyBlock\Generators;

use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use pocketmine\level\generator\Generator;
use pocketmine\level\generator\object\Tree;
use pocketmine\math\Vector3;

class IslandGenerator extends Generator {

    private $settings = [];

    public function __construct(array $settings = [])
    {
        $this->settings = $settings;
    }

    public function getName(): string {
        return "Island Generator";
    }

    public function generateChunk(int $chunkX, int $chunkZ): void {
        $chunk = $this->level->getChunk($chunkX, $chunkZ);
        $chunk->setGenerated();
        if($chunkX == 0 && $chunkZ == 0) {
            for($x = 6; $x < 12; $x++) {
                for($z = 6; $z < 12; $z++) {
                    $chunk->setBlock($x, 61, $z, Block::DIRT);
                    $chunk->setBlock($x, 62, $z, Block::DIRT);
                    $chunk->setBlock($x, 63, $z, Block::GRASS);
                }
            }
            for($airX = 9; $airX < 12; $airX++) {
                for($airZ = 9; $airZ < 12; $airZ++) {
                    $chunk->setBlock($airX, 61, $airZ, Block::AIR);
                    $chunk->setBlock($airX, 62, $airZ, Block::AIR);
                    $chunk->setBlock($airX, 63, $airZ, Block::AIR);
                }
            }
            Tree::growTree($this->level, 11, 64, 6, $this->random, 0);
            $chunk->setBlock(7, 64, 10, Block::CHEST);
            $chunk->setX($chunkX);
            $chunk->setZ($chunkZ);
            $this->level->setChunk($chunkX, $chunkZ, $chunk);
        }
    }

    public static function getWorldSpawn(): Vector3 {
        return new Vector3(7, 66, 7);
    }

    public static function getChestPosition(): Vector3 {
        return new Vector3(7, 64, 10);
    }

    public function populateChunk(int $chunkX, int $chunkZ): void
    {
        return;
    }

    /**
     * @return mixed[]
     * @phpstan-return array<string, mixed>
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    public function getSpawn(): Vector3
    {
        return new Vector3(7, 66, 7);
    }
}