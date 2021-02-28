<?php

namespace App\Entity;

class Orc extends Warrior
{
    public function orc_action(Warrior $warrior)
    {
        $warrior->getDamage(5);
    }
}
