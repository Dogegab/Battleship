<?php

namespace App\Entity;

use App\Repository\ShipRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShipRepository::class)]
class Ship
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'ships')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Board $board = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null; // e.g., "Carrier", "Battleship", "Cruiser", "Submarine", "Destroyer"

    #[ORM\Column]
    private ?int $size = null; // Size of the ship in number of tiles

    #[ORM\Column]
    private ?int $startRow = null; // Starting row of the ship

    #[ORM\Column]
    private ?int $startCol = null; // Starting column of the ship

    #[ORM\Column(length: 1)]
    private ?string $orientation = null; // 'H' for horizontal, 'V' for vertical

    #[ORM\Column]
    private ?bool $isSunk = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBoard(): ?Board
    {
        return $this->board;
    }

    public function setBoard(?Board $board): static
    {
        $this->board = $board;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(int $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getStartRow(): ?int
    {
        return $this->startRow;
    }

    public function setStartRow(int $startRow): static
    {
        $this->startRow = $startRow;

        return $this;
    }

    public function getStartCol(): ?int
    {
        return $this->startCol;
    }

    public function setStartCol(int $startCol): static
    {
        $this->startCol = $startCol;

        return $this;
    }

    public function getOrientation(): ?string
    {
        return $this->orientation;
    }

    public function setOrientation(string $orientation): static
    {
        $this->orientation = $orientation;

        return $this;
    }

    public function getIsSunk(): ?bool
    {
        return $this->isSunk;
    }

    public function setIsSunk(bool $isSunk): static
    {
        $this->isSunk = $isSunk;

        return $this;
    }
}