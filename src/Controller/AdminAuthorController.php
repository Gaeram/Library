<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;


class AdminAuthorController extends AbstractController

 // CRUD ETAPES
 // ETAPE DU FORMULAIRE DE CRÉATION
{
    #[Route("/admin/insert-author", name:"admin-insert-author")]
    public function insertAuthor(EntityManagerInterface $entitytManager, Request $request){
        $author= new Author();

        $form = $this->createForm(AuthorType::class, $author);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form-> isValid()){
            $entitytManager->persist($author);
            $entitytManager->flush();
        }

        return $this->render('admin/form_author.html.twig',[
            'form'=> $form->createView()
        ]);
    }



 // ETAPE DE LA LECTURE DE DONNEES
    #[Route("/admin/authors", name: "admin-authors")]
    public function showAuthors(AuthorRepository $authorRepository){
        $authors = $authorRepository->findAll();

        return $this->render('admin/listauthors.html.twig', [
            'authors' => $authors
        ]);
    }


    #[Route("/admin/author/{id}", name: "admin-author")]
    public function showAuthor(AuthorRepository $authorRepository, $id){
        $authors = $authorRepository->find($id);

        return $this->render('admin/author.html.twig', [
            'author'=>$authors
        ]);
    }

    #[Route("/admin/author/delete/{id}", name: "admin-author-delete")]
    public function deleteAuthor($id, AuthorRepository $authorRepository, EntityManagerInterface $entityManager){
        $author = $authorRepository->find($id);

        if(!is_null($author)) {
            // La fonctionnalité remove efface l'élément selectionné
            $entityManager->remove($author);
            $entityManager->flush();
            // Le redirect to route permet de rediriger vers la page précédent la suppression
            // Le Addflash permet d'afficher un message avertissant si l'opération à
            // été menée à bien ou non
        }

        return $this->redirectToRoute('admin-authors');

    }

    #[Route("/admin/author/update/{id}", name: "admin-author-update")]
    public function updateAuthor($id, AuthorRepository $authorRepository, EntityManagerInterface $entityManager, Request $request)
    {
        $author = $authorRepository->find($id);

        // Création du formulaire en recuperant l'instance
        $form = $this->createForm(AuthorType::class, $author);
        //Renvoi du formulaire sur la page en twig via le biais de la fonction form

        // on donne à la variable qui contient le form
        // une instance de la classe request
        // pour que le form puisse récuperer toutes les données
        // des inputs et faire les setter automatiquement sur $category
        $form->handleRequest($request);

        // ici on note que si le contenu du formulaire est envoyé et est conforme
        // à ce qui est attendu en BDD, il sera pris en compte
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($author);
            $entityManager->flush();
        }

        return $this->render('admin/form_author.html.twig', [
            'form' => $form->createView()
        ]);
    }
}