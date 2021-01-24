<?php

declare(strict_types=1);

namespace KingOfTurkey38\SkyBlock\Tasks;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

class MySQLQueryTask extends AsyncTask {

    private $query;

    public function __construct(array $querys)
    {
        $this->query = $querys;
    }

    public function onRun()
    {
        $con = mysqli_connect("", "", "K", "", 3306);
        foreach($this->query as $query) {
            $con->query($query);
        }
    }
}