<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
// logger
use Psr\Log\LoggerInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(name="bees_nest")
 */
class BeesNest
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $queenCount;

    /**
     * @ORM\Column(type="integer")
     */
    private $queenHitPoints;

    /**
     * @ORM\Column(type="integer")
     */
    private $workerHitPoints;

    /**
     * @ORM\Column(type="integer")
     */
    private $scoutHitPoints;

    /**
     * @ORM\Column(type="integer")
     */
    private $workerCount;

    /**
     * @ORM\Column(type="integer")
     */
    private $scoutCount;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
    * @var LoggerInterface
    */

    public function __construct()
    {
        $this->queenHitPoints = 100;
        $this->workerHitPoints = 50;
        $this->scoutHitPoints = 30;
        $this->queenCount = 1;
        $this->workerCount = 5;
        $this->scoutCount = 8;
        $this->createdAt = new \DateTime();
    }

    // public function getId(): ?int
    // {
    //     return $id;
    // }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getQueenHitPoints(): int
    {
        return $this->queenHitPoints;
    }

    public function setQueenHitPoints(int $queenHitPoints): self
    {
        $this->queenHitPoints = $queenHitPoints;
        return $this;
    }

    public function getWorkerHitPoints(): int
    {
        return $this->workerHitPoints;
    }

    public function setWorkerHitPoints(int $workerHitPoints): self
    {
        $this->workerHitPoints = $workerHitPoints;
        return $this;
    }

    public function getScoutHitPoints(): int
    {
        return $this->scoutHitPoints;
    }

    public function setScoutHitPoints(int $scoutHitPoints): self
    {
        $this->scoutHitPoints = $scoutHitPoints;
        return $this;
    }

    public function getWorkerCount(): int
    {
        return $this->workerCount;
    }

    public function setWorkerCount(int $workerCount): self
    {
        $this->workerCount = $workerCount;
        return $this;
    }

    public function getScoutCount(): int
    {
        return $this->scoutCount;
    }

    public function setScoutCount(int $scoutCount): self
    {
        $this->scoutCount = $scoutCount;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getQueenCount(): int
    {
        return $this->queenCount;
    }

    public function setQueenCount(int $queenCount): self
    {
        $this->queenCount = $queenCount;
        return $this;
    }

    public function hitQueen($logger): void
    {
        // log the scout hit
        $logger->info('Scout hit' . $this->queenHitPoints);
        $this->queenHitPoints -= 15;

        if ($this->queenHitPoints <= 0) {
            $this->queenHitPoints = 0;
            $this->workerHitPoints = 0;
            $this->scoutHitPoints = 0;
            $this->queenCount = 0;
            $this->workerCount = 0;
            $this->scoutCount = 0;
        }
    }

    public function hitWorker($logger): void
    {
        if ($this->workerCount > 0) {
            // log the scout hit
            $logger->info('Scout hit' . $this->workerHitPoints);
            
            $this->workerHitPoints -= 20;
            
            if ($this->workerHitPoints <= 0) {
                $this->workerHitPoints = 0;
                $this->workerCount--;

                // Reset worker hit points for next worker
                if ($this->workerCount > 0) {
                    $this->workerHitPoints = 50;
                }
            }
        }
    }

    public function hitScout($logger): void
    {
        if ($this->scoutCount > 0) {
            // log the scout hit
            $logger->info('Scout hit' . $this->scoutHitPoints);
            $this->scoutHitPoints -= 15;
            
            if ($this->scoutHitPoints <= 0) {
                $this->scoutCount--;
                $this->scoutHitPoints = 0;
                // Reset scout hit points for next scout
                if ($this->scoutCount > 0) {
                    $this->scoutHitPoints = 30;
                }
            }
        }
    }

    public function getNumberOfBees(): int
    {
        return $this->queenCount + $this->workerCount + $this->scoutCount;
    }
}
