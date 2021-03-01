<?php

namespace App\Entity;


abstract class Warrior
{
    protected $name;
    protected $health;
    protected $plague = false;
    protected $degats;

    public function __construct(string $name, int $health)
    {
        $this->name = $name;
        $this->health = $health;
    }

    /**
     * Get the value of health
     */
    public function getHealth()
    {
        return $this->health;
    }

    /**
     * Set the value of health
     *
     * @return  self
     */
    public function setHealth($health)
    {
        $this->health = $health;

        return $this;
    }

    /**
     * Get the value of name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of plague
     */
    public function getPlague()
    {
        return $this->plague;
    }

    /**
     * Set the value of plague
     *
     * @return  self
     */
    public function setPlague()
    {
        $this->plague = true;
        return $this;
    }


    public function getDamage(int $degats)
    {
        $this->health -= $degats;
        return $this;
    }

    public function plague()
    {
        if ($this->getPlague()) {
            $this->getDamage(3);
            return $this->getName() . ' est empoisonné et a subi 3 points de dégats <br>';
        }
        return false;
    }

    public function maybeSuccumbs($playersAlive): bool
    {
        if ($this->getHealth() <= 0) {
            $offset = array_search($this, $playersAlive);
            array_splice($playersAlive, $offset, 1);
            return $playersAlive;
        }
        return false;
    }

    public function getRandomMethod(): string
    {
        //----------- actions possibles de l'entité
        $action_methods = preg_grep('/_action/', get_class_methods($this));
        //---------------------- methode aléatoire
        return $action_methods[rand(0, count($action_methods) - 1)];
    }

    public function fight($randMethod, Warrior $victim)
    {
        if ($randMethod == 'heal_action') { // Witch self healing
            $this->$randMethod($this);
        } else {
            $this->$randMethod($victim);
        }
    }

    public function searchRandomTarget($playersAlive): Warrior
    {
        $targets = array_slice($playersAlive, 0); // copie du tableau
        $selfOffset =  array_search($this, $playersAlive); // recherche le propre index du joueur
        //---------------------- tableau intermédiare pour retirer le jour de la liste des cibles
        array_splice($targets, $selfOffset, 1);
        $random_target =  count($targets) > 0 ? $targets[rand(0, count($targets) - 1)] : $targets[0];
        return $random_target;
    }
    
}
