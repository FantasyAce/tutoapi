<?php

namespace App\Controller;

use App\Entity\Person;
use App\Repository\PersonRepository;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;

class PersonController extends AbstractController
{
    /**
     * @Route("/person", name="person_list", methods={"GET", "HEAD"})
     */
    public function index()
    {

        $persons =$this->getDoctrine()->getRepository(Person::class)->findAll();
        return $this->json($persons);
    }

    /**
     * @Route("/person/{id}", name="person_view", methods={"GET", "HEAD"}, requirements={
     * "id"="\d+"
     * })
     */

    public function personView($id){
        $persons = $this->getDoctrine()->getRepository(Person::class)->find($id);

    if($persons){
        return $this->json($persons);

    }else{
        return $this->json([
            "error_message"=> "No person found with this id :".$id
        ], 404);
    }
     
    }
    /**
     * @Route("/person", name="person_create", methods={"POST"}) 
     * */ 

    public function personCreate(Request $request, SerializerInterface $serializer, EntityManagerInterface $em){
        $data = $request->getContent();
       
        //déserializer les données textuelles en json pour instancier un person
        $person = $serializer->deserialize($data,Person::class, 'json');

        $em->persist($person);
        $em->flush();

        return $this->json([
                "id"=>$person->getId(),
                
                "url"=>$this->generateUrl(
                    "person_view",
                    [
                        "id"=> $person->getId()
                    ],
                    UrlGeneratorInterface::ABSOLUTE_URL
                )
            ]
        );
    }

    /**
     * @Route("/person/{id}", name="person_delete", methods={"DELETE"}, requirements={
     * "id"="\d+"
     * })
     */

    public function personDelete($id, EntityManagerInterface $em, PersonRepository $r){
        $person = $r->find($id);
        if($person){
            $em->remove($person);
            $em ->flush();

            return $this->json([
                "message"=> "person '%s' successfully deleted!", $person->getLastName()
            ]);
        }else{
            return $this->json([
                "error_message" => "No person found with this id:" .$id
                
            ], 404);

        }        

    }

    /**
     * @Route("/person/{id}", name="person_update", methods={"PUT"}, requirements={
     * "id" = "\d+"
     * })
     */

    public function personUpdate($id,EntityManagerInterface $em, personRepository $r, Request $request, SerializerInterface $serializer){
        $person = $r->find($id);

        if($person){
            $data = $request->getContent();
            //on peut pointer au serializer une entité existante sur laquelle effectuer des modifications
            //avec l'option object_to_populate
            $personUpdate = $serializer->deserialize($data, Person::class, 'json', [
                'object_to_populate'=>$person
            ]);
            //l'entite étant déjà suivie par doctrine il nous suffit de lancer flush pour appliquer la modif
            $em->flush();

            return $this->json(
                $personUpdate
            );

        }else{
            return $this->json([
                "error_message"=>"No person found with that id" .$id
            ]);
        }

    }
}