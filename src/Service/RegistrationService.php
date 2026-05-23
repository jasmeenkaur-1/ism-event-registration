<?php
namespace App\Service;

use App\Entity\Registration;
use App\Entity\Summit;
use App\Repository\RegistrationRepository;
use Doctrine\ORM\EntityManagerInterface;

class RegistrationService
{
    public function __construct(
        private EntityManagerInterface $em,
        private RegistrationRepository $registrationRepository
    ) {}

    public function register(Registration $registration, Summit $summit): array
    {
        $count = $this->registrationRepository->count(['summit' => $summit]);
        $capacity = $summit->getLocation()->getCapacity();

        if ($count >= $capacity) {
            return ['success' => false];
        }

        $registration->setSummit($summit);
        $registration->setRegisteredAt(new \DateTime());
        $registration->setTicketNumber('ISM-' . strtoupper(substr(md5(uniqid()), 0, 8)));

        $this->em->persist($registration);
        $this->em->flush();

        return ['success' => true, 'id' => $registration->getId()];
    }
}