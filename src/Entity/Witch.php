<?php

namespace App\Entity;

class Witch extends Warrior
{
    public function poison_action(Warrior $warrior)
    {
        $warrior->getDamage(3)->setPlague(true);
    }

    public function heal_action()
    {
        $health = $this->getHealth();
        $this->setHealth($health + 4);
    }
}
