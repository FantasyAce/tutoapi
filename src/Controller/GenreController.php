<?php

namespace App\Controller;

use App\Entity\Genre;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
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
                    ]
                )
            ]
        );
    }
}
