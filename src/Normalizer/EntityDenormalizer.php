<?php

namespace App\Normalizer;

use App\Entity\Movie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;

class EntityDenormalizer implements ContextAwareDenormalizerInterface
{
    private $entityManager;
    
    public function __construct(EntityManagerInterface $em)
    {
        $this->entityManager=$em;
    }

    public function supportsDenormalization($data, $type, $format = null, array $context = [])
    {
        return (strpos($type, 'App\\Entity\\') === 0) &&
        (is_numeric($data) || is_string($data) || is_array($data) && isset($data['id']));   
    }

    public function denormalize($data, $type, $format = null, array $context =[])
    {
        return $this->entityManager->find($type, $data);
    }
}