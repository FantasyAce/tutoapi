<?php

namespace App\Controller;

use App\Entity\Genre;
use App\Repository\GenreRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;

class GenreController extends AbstractController
{
    /**
     * @Route("/genre", name="genre_list", methods={"GET", "HEAD"})
     */
    public function index()
    {

        $genres =$this->getDoctrine()->getRepository(Genre::class)->findAll();
        return $this->json($genres);
    }

    /**
     * @Route("/genre/{id}", name="genre_view", methods={"GET", "HEAD"}, requirements={
     * "id"="\d+"
     * })
     */

    public function genreView($id){
        $genres = $this->getDoctrine()->getRepository(Genre::class)->find($id);

    if($genres){
        return $this->json($genres);

    }else{
        return $this->json([
            "error_message"=> "No genre found with this id :".$id
        ], 404);
    }
     
    }
    /**
     * @Route("/genre", name="genre_create", methods={"POST"}) 
     * */ 

    public function genreCreate(Request $request, SerializerInterface $serializer, EntityManagerInterface $em){
        $data = $request->getContent();

        //déserializer les données textuelles en json pour instancier un Genre
        $genre = $serializer->deserialize($data, Genre::class, 'json');

        $em->persist($genre);
        $em->flush();

        return $this->json([
                "id"=>$genre->getId(),
                "url"=>$this->generateUrl(
                    "genre_view",
                    [
                        "id"=> $genre->getId()
                    ],
                    UrlGeneratorInterface::ABSOLUTE_URL
                )
            ]
        );
    }

    /**
     * @Route("/genre/{id}", name="genre_delete", methods={"DELETE"}, requirements={
     * "id"="\d+"
     * })
     */

    public function genreDelete($id, EntityManagerInterface $em, GenreRepository $r){
        $genre = $r->find($id);
        if($genre){
            $em->remove($genre);
            $em ->flush();

            return $this->json([
                "message"=> "Genre '%s' successfully deleted!", $genre->getName()
            ]);
        }else{
            return $this->json([
                "error_message" => "No genre found with this id:" .$id
                
            ], 404);

        }        

    }

    /**
     * @Route("/genre/{id}", name="genre_update", methods={"PUT"}, requirements={
     * "id" = "\d+"
     * })
     */

    public function genreUpdate($id,EntityManagerInterface $em, GenreRepository $r, Request $request, SerializerInterface $serializer){
        $genre = $r->find($id);

        if($genre){
            $data = $request->getContent();
            //on peut pointer au serializer une entité existante sur laquelle effectuer des modifications
            //avec l'option object_to_populate
            $genreUpdate = $serializer->deserialize($data, Genre::class, 'json', [
                'object_to_populate'=>$genre
            ]);
            //l'entite étant déjà suivie par doctrine il nous suffit de lancer flush pour appliquer la modif
            $em->flush();

            return $this->json(
                $genreUpdate
            );

        }else{
            return $this->json([
                "error_message"=>"No genre found with that id" .$id
            ]);
        }

    }
}
