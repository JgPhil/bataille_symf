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
            'players_alive' => $this->playersAlive
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
        $playersArray = $this->playersAlive;
        
        if (count($playersArray) > 1) {
            for ($i = 0; $i < count($playersArray); $i++) {
                $warrior = $playersArray[$i];
                $this->summary .= $warrior->plague();
                $warrior->maybeSuccumbs($playersArray);
                $this->summary .= !in_array($warrior, $playersArray) ?
                    $warrior->getName() . ' a succombé ' : '';
                $method = $warrior->getRandomMethod();
                $target = $warrior->searchRandomTarget($playersArray);
                $initialHealth = $target->getHealth();
                $warrior->$method($target); ////////////ATTTAAAACCKK
                $damages = $initialHealth - $target->getHealth();
                if ($method == 'heal_action') {
                    $this->summary .=
                        $warrior->getName() . ' s\'est soigné de 3 points de vie';
                } else {
                    $this->summary .=
                        $warrior->getName() . ' a attaqué '
                        . $target->getName() . ' avec ' . $method . ' et lui a infligé '
                        . $damages . ' dégats ';
                    $this->summary .=
                        $target->maybeSuccumbs($playersArray);
                    $this->summary .= !in_array($target, $playersArray) ?
                        $target->getName() . ' a succombé ' : '';
                }
            }
        } else {
            $this->summary .= "Le vainqueur est " . $playersArray[0]->getName()
                . ' il lui reste ' . $playersArray[0]->getHealth() . ' PV';
        }
        // After a turn, data is sent via ajax response to view
        $data = [
            'summary' => $this->summary,
            'status' => $this->getPlayersStatus()
        ];
        
        return $this->json($data);
    }
}
