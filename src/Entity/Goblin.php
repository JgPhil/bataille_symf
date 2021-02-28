<?php

namespace App\Entity;

class Goblin extends Warrior
{
    public function normal_action(Warrior $warrior)
    {
        $warrior->getDamage(5);
    }

    public function massive_action(Warrior $warrior)
    {
        $warrior->getDamage(10);
        $this->getDamage(3);
    }
}
