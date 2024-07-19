<?php

namespace App\Controller;

use App\Entity\Ship;
use App\Repository\ShipRepository;
use App\Repository\BoardRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse; // Utiliser JsonResponse pour les réponses JSON
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ShipController extends AbstractController
{
    #[Route(path:'/game/save-ships', name: 'save_ships')]
    public function saveShips(Request $request, 
                            EntityManagerInterface $entityManager, 
                            BoardRepository $boardRepository,
                            ShipRepository $shipRepository)
    {
        $data = json_decode($request->getContent(), true);

        // Vérifiez si $data est un tableau et n'est pas vide
        if (!is_array($data) || empty($data)) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Invalid data'
            ], 400); // Réponse JSON avec un code d'erreur HTTP
        }

        $ships = $data;

        // Récupérer le board du joueur
        $board = $boardRepository->findOneBy(['name' => 'player']);
        if (!$board) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Board not found',
                'redirect' => $this->generateUrl('app_game') // Redirection en cas d'erreur
            ], 400); // Réponse JSON avec un code d'erreur HTTP
        }

        // Récupérer les navires associés au board du joueur
        $existingShips = $shipRepository->findBy(['board' => $board]);

        // Créer un tableau pour retrouver les navires par type et leur ID
        $existingShipsMap = [];
        foreach ($existingShips as $ship) {
            $existingShipsMap[$ship->getType()] = $ship;
        }

        // Boucler sur les données des navires et les mettre à jour
        foreach ($ships as $shipData) {
            $shipType = $shipData['type'];
            if (isset($existingShipsMap[$shipType])) {
                // Mettre à jour le navire existant
                $ship = $existingShipsMap[$shipType];
                $ship->setStartRow($shipData['x']);
                $ship->setStartCol($shipData['y']);
                $ship->setOrientation($shipData['orientation']);
                $entityManager->persist($ship);
            } else {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Ship type not found in the database',
                    'redirect' => $this->generateUrl('app_game')
                ], 400); // Réponse JSON avec un code d'erreur HTTP
            }
        }

        // Sauvegarder toutes les entités en une seule transaction
        $entityManager->flush();
        return new JsonResponse([
            'success' => true,
            'message' => 'Les navires ont été enregistrés avec succès !',
            'redirect' => $this->generateUrl('launched_game') // Redirection en cas de succès
        ], 200); // Réponse JSON avec un code de succès HTTP
    }
}