<?php

namespace App\Service;

use App\Entity\Pet;
use App\Repository\PetRepository;

class PetManager
{
    /**
     * @var PetRepository
     */
    private $petRepository;

    public function __construct(PetRepository $petRepository)
    {
        $this->petRepository = $petRepository;

    }

    public function addPet(Pet $pet)
    {

        $this->save($pet);
    }

    public function save(Pet $pet)
    {
        $this->petRepository->save($pet);
    }

    public function findByStatus($status)
    {
        return $this->petRepository->findBy([
            'status' => $status
        ]);
    }

    public function delete($pet)
    {
        $this->petRepository->delete($pet);
    }

}