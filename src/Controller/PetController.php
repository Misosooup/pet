<?php

namespace App\Controller;

use App\Entity\Pet;
use App\Exception\InvalidFormException;
use App\Form\PetType;
use App\Service\PetManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\SerializerInterface;

class PetController extends AbstractController
{
    /**
     * @var PetManager
     */
    private $petManager;
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(
        PetManager $petManager,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        SerializerInterface $serializer
    ) {
        $this->petManager = $petManager;
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->serializer = $serializer;
    }

    /**
     * @Rest\Post("pets")
     * @param Request $request
     *
     * @return JsonResponse
     * @throws InvalidFormException
     */
    public function postPetAction(Request $request)
    {
        try {
            $this->entityManager->beginTransaction();

            $pet = $this->processForm($request, PetType::class);
            $this->petManager->addPet($pet);
            $this->entityManager->commit();

            return $this->serialize($pet);
        } catch (HttpException $e) {
            $this->entityManager->rollback();
            $this->logger->log(LogLevel::CRITICAL, $e->getMessage());
            $this->logger->log(LogLevel::CRITICAL, $e->getTraceAsString());

            return new JsonResponse([
                'code' => $e->getStatusCode(),
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * @Rest\Put("pets/{pet}")
     * @param Request $request
     * @param Pet     $pet
     *
     * @return JsonResponse
     */
    public function putPetAction(Request $request, Pet $pet)
    {
        try {
            $this->entityManager->beginTransaction();

            $pet = $this->processForm($request, PetType::class, $pet);
            $this->petManager->save($pet);
            $this->entityManager->commit();

            return $this->serialize($pet);
        } catch (HttpException $e) {
            $this->entityManager->rollback();
            $this->logger->log(LogLevel::CRITICAL, $e->getMessage());
            $this->logger->log(LogLevel::CRITICAL, $e->getTraceAsString());
            return new JsonResponse([
                'code' => $e->getStatusCode(),
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * @Rest\Get("pets/findByStatus")
     * @param Request $request
     *
     * @return \App\Entity\Pet[]
     * @throws Exception
     */
    public function getPetByStatus(Request $request)
    {
        if (!$request->query->has('status')) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'status not provided in query string');
        }

        try {
            $pets = $this->petManager->findByStatus($request->query->get('status'));

            return $this->serialize($pets);
        } catch (HttpException $e) {
            $this->logger->log(LogLevel::CRITICAL, $e->getMessage());
            $this->logger->log(LogLevel::CRITICAL, $e->getTraceAsString());
            return new JsonResponse([
                'code' => $e->getStatusCode(),
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * @Rest\Get("pets/{pet}")
     * @param Pet $pet
     *
     * @return JsonResponse
     */
    public function getPetAction(Pet $pet)
    {
        $jsonContent = $this->serializer->serialize($pet, 'json');

        return new JsonResponse(json_decode($jsonContent));
    }

    /**
     * @Rest\Delete("pets/{pet}")
     * @param Pet $pet
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function deletePetAction(Pet $pet)
    {
        try {
            $this->entityManager->beginTransaction();
            $this->petManager->delete($pet);
            $this->entityManager->commit();
            return $this->serialize($pet);
        } catch (HttpException $e) {
            $this->entityManager->rollback();
            $this->logger->log(LogLevel::CRITICAL, $e->getMessage());
            $this->logger->log(LogLevel::CRITICAL, $e->getTraceAsString());
            return new JsonResponse([
                'code' => $e->getStatusCode(),
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * @param Request $request
     * @param string  $formType
     * @param null    $data
     * @param array   $options
     *
     * @return mixed
     * @internal param string $form
     */
    private function processForm(Request $request, $formType, $data = null, $options = [])
    {
        $form = $this->formFactory->createNamed('', $formType, $data, array_merge(
            ['method' => $request->getMethod()],
            $options
        ));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            return $form->getData();
        } else {
            throw new InvalidFormException((string) $form->getErrors(true, false));
        }
    }

    private function serialize($data)
    {
        $jsonContent = $this->serializer->serialize($data, 'json');

        return new JsonResponse(json_decode($jsonContent));
    }
}