<?php

namespace App\Entity;

use App\Entity\Tile;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity]
class Board
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'board', targetEntity: Tile::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $tiles;

    #[ORM\OneToMany(mappedBy: 'board', targetEntity: Ship::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $ships;

    public function __construct()
    {
        $this->tiles = new ArrayCollection();
        $this->ships = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Tile>
     */
    public function getTiles(): Collection
    {
        return $this->tiles;
    }

    public function addTile(Tile $tile): static
    {
        if (!$this->tiles->contains($tile)) {
            $this->tiles->add($tile);
            $tile->setBoard($this);
        }

        return $this;
    }

    public function removeTile(Tile $tile): static
    {
        if ($this->tiles->removeElement($tile)) {
            // set the owning side to null (unless already changed)
            if ($tile->getBoard() === $this) {
                $tile->setBoard(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Ship>
     */
    public function getShips(): Collection
    {
        return $this->ships;
    }

    public function addShip(Ship $ship): static
    {
        if (!$this->ships->contains($ship)) {
            $this->ships->add($ship);
            $ship->setBoard($this);
        }

        return $this;
    }

    public function removeShip(Ship $ship): static
    {
        if ($this->ships->removeElement($ship)) {
            // set the owning side to null (unless already changed)
            if ($ship->getBoard() === $this) {
                $ship->setBoard(null);
            }
        }

        return $this;
    }
}