<?php

namespace App\Controller;

use App\Entity\Goblin;
use App\Entity\Orc;
use App\Entity\Witch;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BattleController extends AbstractController
{
    private $goblin;
    private $witch;
    private $orc;
    private $playersAlive;
    private $players;
    private $summary = "";


    public function __construct()
    {
        $this->goblin = new Goblin('Goblin', 100);
        $this->witch = new Witch('Witch', 50);
        $this->orc = new Orc('Orc', 100);
        $this->playersAlive = [$this->goblin, $this->witch, $this->orc];
        $this->players = array_slice($this->playersAlive, 0);
    }


    /**
     * @Route("/", name="battle")
     */
    public function index(): Response
    {
        return $this->render('battle.html.twig', [
            'players_alive' => $this->playersAlive,
            'players' => $this->players
        ]);
    }

    /**
     * @Route("/status", name="status")
     */
    public function getPlayersStatus()
    {
        return $this->json($this->playersAlive);
    }

    /**
     * @Route("/next-turn", name="next-turn")
     */
    public function nextTurn()
    {
        if (count($this->playersAlive) > 1) {
            for ($i = 0; $i < count($this->playersAlive); $i++) {
                $this->summary .= $this->playersAlive[$i]->plague();
                $this->playersAlive[$i]->maybeSuccumbs($this->playersAlive);
                $this->summary .= !in_array($this->playersAlive[$i], $this->playersAlive) ?
                    $this->playersAlive[$i]->getName() . ' a succombé ' : '';
                $method = $this->playersAlive[$i]->getRandomMethod();
                $target = $this->playersAlive[$i]->searchRandomTarget($this->playersAlive);
                $initialHealth = $target->getHealth();
                $this->playersAlive[$i]->$method($target);
                $damages = $initialHealth - $target->getHealth();
                $this->summary .=
                    $this->playersAlive[$i]->getName() . ' a attaqué '
                    . $target->getName() . ' avec ' . $method . ' et lui a infligé '
                    . $damages . ' dégats ';
                $this->summary .=
                    $target->maybeSuccumbs($this->playersAlive);
                $this->summary .= !in_array($target, $this->playersAlive) ?
                    $target->getName() . ' a succombé ' : '';
            }
        } else {
            $this->summary .= "Le vainqueur est " . $this->playersAlive[0]->getName()
                . ' il lui reste ' . $this->playersAlive[0]->getHealth() . ' PV';
        }
        $data = [$this->summary, $this->getPlayersStatus()];
        return $this->json($data);
    }
}
