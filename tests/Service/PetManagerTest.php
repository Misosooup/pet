<?php

namespace App\Tests\Service;

use App\Entity\Pet;
use App\Repository\PetRepository;
use App\Service\PetManager;
use Phake;
use Phake_IMock;
use PHPUnit\Framework\TestCase;

class PetManagerTest extends TestCase
{
    /** @var PetRepository|Phake_IMock */
    private $repository;

    /** @var PetManager */
    private $manager;

    protected function setUp(): void
    {
        $this->repository = Phake::mock(PetRepository::class);
        $this->manager = new PetManager($this->repository);

    }

    public function testAddPet(): void
    {
        $pet = Phake::mock(Pet::class);

        $this->manager->addPet($pet);

        Phake::verify($this->repository)->save($pet);
    }

    public function testSave(): void
    {
        $pet = Phake::mock(Pet::class);

        $this->manager->save($pet);

        Phake::verify($this->repository)->save($pet);
    }

    public function testFindByStatus(): void
    {
        $status = 'test';
        $this->manager->findByStatus($status);
        Phake::verify($this->repository)->findBy([
            'status' => $status
        ]);
    }

    public function testDelete(): void
    {
        $pet = Phake::mock(Pet::class);
        $this->manager->delete($pet);
        Phake::verify($this->repository)->delete($pet);
    }
}