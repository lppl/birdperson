<?php

namespace Birdperson;

interface ServerProvider
{
    public function getBestPossibleServer(): Server;

    public function getServer(int $id): Server;
}