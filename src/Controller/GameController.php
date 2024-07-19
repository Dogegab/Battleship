<?php

namespace App\Controller;

use App\Repository\BoardRepository;
use Doctrine\ORM\EntityManager;
use App\Repository\ShipRepository;
use App\Repository\TileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GameController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route(path:'/game', name: 'app_game')]
    public function index(): Response
    {
        return $this->render('game/game.html.twig');
    }

    #[Route('/game/reset', name: 'app_reset')]
    public function reset(
        ShipRepository $shipRepository,
        TileRepository $tileRepository,
        EntityManager $entityManager
    ): Response {
    // Réanitialiser toutes les données existantes
    $ships = $shipRepository->findAll();
    foreach ($ships as $ship){
        switch ($ship->getType()) {
            case 'Carrier':
                $ship->setStartRow(1);
                $ship->setStartCol(1);
                break;
            case 'Battleship':
                $ship->setStartRow(2);
                $ship->setStartCol(1);
                break;
            case 'Cruiser':
                $ship->setStartRow(3);
                $ship->setStartCol(1);
                break;
            case 'Submarine':
                $ship->setStartRow(4);
                $ship->setStartCol(1);
                break;
            case 'Destroyer':
                $ship->setStartRow(5);
                $ship->setStartCol(1);
                break;
        }
    }
    $nonEmptyTiles = $tileRepository->findBy([
    'status' => ['HIT', 'MISS']
]);

    foreach ($nonEmptyTiles as $tile) {
        $tile->setStatus('empty');
    }
    $entityManager->flush();

        return $this->redirectToRoute('app_game'); // Rediriger vers la page d'accueil

    }

    #[Route('/game/board', name: 'app_board')]
    public function game(BoardRepository $boardRepository): Response
    {
        // Fetch the boards by their names or any unique identifier
        $playerBoard = $boardRepository->findOneBy(['name' => 'player']);
        $enemyBoard = $boardRepository->findOneBy(['name' => 'ia']);

        return $this->render('game/board.html.twig', [
            'playerBoard' => $playerBoard,
            'iaBoard' => $enemyBoard,
        ]);
    }

    #[Route('/game/boardgame', name:'launched_game')]
    public function fight(
        BoardRepository $boardRepository
    ): Response {
        $playerBoard = $boardRepository->findOneBy(['name' => 'player']);
        $enemyBoard = $boardRepository->findOneBy(['name' => 'ia']);

        return $this->render('game/boardgame.html.twig', [
            'playerBoard' => $playerBoard,
            'iaBoard' => $enemyBoard
        ]);
    }
}