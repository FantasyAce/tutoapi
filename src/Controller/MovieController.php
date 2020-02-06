<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;

class MovieController extends AbstractController
{
    /**
     * @Route("/movie", name="movie_list", methods={"GET", "HEAD"})
     */
    public function index()
    {

        $movies =$this->getDoctrine()->getRepository(Movie::class)->findAll();
        return $this->json($movies, 200, [], ['groups' => 'movie']);
    }

    /**
     * @Route("/movie/{id}", name="movie_view", methods={"GET", "HEAD"}, requirements={
     * "id"="\d+"
     * })
     */

    public function movieView($id){
        $movies = $this->getDoctrine()->getRepository(Movie::class)->find($id);

    if($movies){
        return $this->json($movies, 200, [], ['groups' => 'movie']);

    }else{
        return $this->json([
            "error_message"=> "No movie found with this id :".$id
        ], 404);
    }
     
    }
    /**
     * @Route("/movie", name="movie_create", methods={"POST"}) 
     * */ 

    public function movieCreate(Request $request, SerializerInterface $serializer, EntityManagerInterface $em){
        $data = $request->getContent();
       
        //déserializer les données textuelles en json pour instancier un movie
        $movie = $serializer->deserialize($data,Movie::class, 'json');

        $em->persist($movie);
        $em->flush();

        return $this->json([
                "id"=>$movie->getId(),
                
                "url"=>$this->generateUrl(
                    "movie_view",
                    [
                        "id"=> $movie->getId()
                    ],
                    UrlGeneratorInterface::ABSOLUTE_URL
                )
            ]
        );
    }

    /**
     * @Route("/movie/{id}", name="movie_delete", methods={"DELETE"}, requirements={
     * "id"="\d+"
     * })
     */

    public function movieDelete($id, EntityManagerInterface $em, MovieRepository $r){
        $movie = $r->find($id);
        if($movie){
            $em->remove($movie);
            $em ->flush();

            return $this->json([
                "message"=> "movie '%s' successfully deleted!", $movie->getTitle()
            ]);
        }else{
            return $this->json([
                "error_message" => "No movie found with this id:" .$id
                
            ], 404);

        }        

    }

    /**
     * @Route("/movie/{id}", name="movie_update", methods={"PUT"}, requirements={
     * "id" = "\d+"
     * })
     */

    public function movieUpdate($id,EntityManagerInterface $em, MovieRepository $r, Request $request, SerializerInterface $serializer){
        $movie = $r->find($id);

        if($movie){
            $data = $request->getContent();
            //on peut pointer au serializer une entité existante sur laquelle effectuer des modifications
            //avec l'option object_to_populate
            $movieUpdate = $serializer->deserialize($data, Movie::class, 'json', [
                'object_to_populate'=>$movie
            ]);
            //l'entite étant déjà suivie par doctrine il nous suffit de lancer flush pour appliquer la modif
            $em->flush();

            return $this->json(
                $movieUpdate
            );

        }else{
            return $this->json([
                "error_message"=>"No movie found with that id" .$id
            ]);
        }

    }
}
