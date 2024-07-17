<?php

namespace App\DataFixtures;

use App\Entity\Ship;
use App\Entity\Tile;
use App\Entity\User;
use App\Entity\Board;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // Création d'un dossier d'image et stockage
        $sourceDir = __DIR__ . '/Data';
        $destinationDir = __DIR__ . '/../../public/uploads/images/posters';
        $filesystem = new Filesystem();
        $filesystem->mkdir($destinationDir);
        $files = scandir($sourceDir);
        foreach ($files as $file) {
            if (is_file($sourceDir . '/' . $file)) {
                $filesystem->copy($sourceDir . '/' . $file, $destinationDir . '/' . $file, true);
            }
        }

        // Création d'un user test
        $user = new User();
        $user->setUsername('GabTest');
        $user->setEmail('Gab@gab.com');
        $hashedPassword = $this->passwordHasher->hashPassword($user, 'azerty');
        $user->setPassword($hashedPassword);
        $manager->persist($user);
        $manager->flush();

         // Création des deux plateaux
        $playerBoard = $this->createBoard($manager);
        $iaBoard = $this->createBoard($manager);
        $playerBoard->setName('ia');
        $iaBoard->setName('player');

        // Création des navires pour chaque plateau
        $this->createShips($playerBoard, $manager);
        $this->createShips($iaBoard, $manager);
     
        // Création de la grille pour chaque plateau
        $this->createGrid($playerBoard, $manager);
        $this->createGrid($iaBoard, $manager);
     
        $manager->flush();

        }
     
        private function createBoard(ObjectManager $manager): Board
        {
            $board = new Board();
            $manager->persist($board);
     
            return $board;
        }
     
        private function createGrid(Board $board, ObjectManager $manager): void
        {
            $gridSize = 10;
            // $row = 1;

            for ($row = 1; $row <= $gridSize; $row++) {
                for ($col = 1; $col <= $gridSize; $col++) {
                    $tile = new Tile();
                    $tile->setX($row);
                    $tile->setY($col);
                    $tile->setBoard($board);
                    $tile->setStatus('empty');
                    $manager->persist($tile);
                }
            }
         }
     
        private function createShips(Board $board, ObjectManager $manager): void
        {
            $shipsData = [
                ['type' => 'Carrier', 'size' => 5, 'startRow' => 1, 'startCol' => 1, 'orientation' => 'H'],
                ['type' => 'Battleship', 'size' => 4, 'startRow' => 2, 'startCol' => 1, 'orientation' => 'H'],
                ['type' => 'Cruiser', 'size' => 3, 'startRow' => 3, 'startCol' => 1, 'orientation' => 'H'],
                ['type' => 'Submarine', 'size' => 3, 'startRow' => 4, 'startCol' => 1, 'orientation' => 'H'],
                ['type' => 'Destroyer', 'size' => 2, 'startRow' => 5, 'startCol' => 1, 'orientation' => 'H'],
            ];
     
            foreach ($shipsData as $shipData) {
                $ship = new Ship();
                $ship->setType($shipData['type']);
                $ship->setSize($shipData['size']);
                $ship->setStartRow($shipData['startRow']);
                $ship->setStartCol($shipData['startCol']);
                $ship->setOrientation($shipData['orientation']);
                $ship->setBoard($board);
                $manager->persist($ship);
            }
        }

}
