<?php

namespace App\Controller;

use App\Entity\BeesNest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

class BeeGameController extends AbstractController
{
    #[Route('/', name: 'bee_game')]
    public function index(LoggerInterface $logger, EntityManagerInterface $entityManager): Response
    {
        // Retrieve or create a BeesNest instance
        $beesNest = $entityManager->getRepository(BeesNest::class)->find(1);
    
        if (!$beesNest) {
            $beesNest = new BeesNest($logger);
            $beesNest->setName('Default Nest');
            $entityManager->persist($beesNest);
            $entityManager->flush();
        }
    
        $bees = [
            'queen' => [
                'type' => 'Queen',
                'hitPoints' => $beesNest->getQueenHitPoints(),
                'damage' => 15,  // The damage value is static as per the rules.
                'count' => 1,    // There is always 1 Queen.
            ],
            'worker' => [
                'type' => 'Worker',
                'hitPoints' => $beesNest->getWorkerHitPoints(),
                'damage' => 20,  // The damage value is static as per the rules.
                'count' => $beesNest->getWorkerCount(),
            ],
            'scout' => [
                'type' => 'Scout',
                'hitPoints' => $beesNest->getScoutHitPoints(),
                'damage' => 15,  // The damage value is static as per the rules.
                'count' => $beesNest->getScoutCount(),
            ],
        ];
    
        return $this->render('bee_game/index.html.twig', [
            'beesNest' => $beesNest,
            'bees' => $bees,
        ]);
    }

    #[Route('/hit', name: 'hit_bee')]
    public function hit(LoggerInterface $logger, EntityManagerInterface $entityManager): Response
    {
        $beesNest = $entityManager->getRepository(BeesNest::class)->find(1);
    
        if ($beesNest) {
            // $beeTypes = ['queen', 'worker', 'scout'];
            // get bee type from bees left using getWorkerCount, getScoutCount, getQueenCount
            $beeTypes = [];
            if ($beesNest->getQueenCount() > 0) {
                $beeTypes[] = 'queen';
            }
            if ($beesNest->getWorkerCount() > 0) {
                $beeTypes[] = 'worker';
            }
            if ($beesNest->getScoutCount() > 0) {
                $beeTypes[] = 'scout';
            }
            $selectedBeeType = $beeTypes[array_rand($beeTypes)];

            switch ($selectedBeeType) {
                case 'queen':
                    $beesNest->hitQueen($logger);
                    break;
                case 'worker':
                    $beesNest->hitWorker($logger);
                    break;
                case 'scout':
                    $beesNest->hitScout($logger);
                    break;
            }
    
            $entityManager->flush();
        }

        // if all bees are dead, reset the game
        if ($beesNest->getNumberOfBees() === 0) {
            return $this->redirectToRoute('reset_bee');
        }
        return $this->redirectToRoute('bee_game');
    }

    // reset
    #[Route('/reset', name: 'reset_bee')]
    public function reset(EntityManagerInterface $entityManager): Response
    {
        $beesNest = $entityManager->getRepository(BeesNest::class)->find(1);
    
        if ($beesNest) {
            $beesNest->setQueenHitPoints(100);
            $beesNest->setWorkerHitPoints(75);
            $beesNest->setScoutHitPoints(50);
            $beesNest->setQueenCount(1);
            $beesNest->setWorkerCount(5);
            $beesNest->setScoutCount(8);

            $entityManager->flush();
        }

        return $this->redirectToRoute('bee_game');
    }
}
