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
        return $this->playersAlive;
    }

    /**
     * @Route("/next-turn", name="next-turn")
     */
    public function nextTurn()
    {
        $initialStatus = $this->getPlayersStatus();
        if (count($this->playersAlive) > 1) {
            for ($i = 0; $i < count($this->playersAlive); $i++) {
                $warrior = $this->playersAlive[$i];
                $this->summary .= $warrior->plague();
                $warrior->maybeSuccumbs($this->playersAlive);
                $this->summary .= !in_array($warrior, $this->playersAlive) ?
                $warrior->getName() . ' a succombé ' : '';
                $method = $warrior->getRandomMethod();
                $target = $warrior->searchRandomTarget($this->playersAlive);
                $initialHealth = $target->getHealth();
                $warrior->$method($target); ////////////ATTTAAAACCKK
                $damages = $initialHealth - $target->getHealth();
                $this->summary .=
                    $warrior->getName() . ' a attaqué '
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
        $data = [
            'summary' => $this->summary,
            'status' => $this->getPlayersStatus()
        ];
        return $this->json($data);
    }
}
