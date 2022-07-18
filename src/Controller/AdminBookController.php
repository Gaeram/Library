<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Book;
use App\Repository\BookRepository;
use App\Form\BookType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;


class AdminBookController extends AbstractController
{
    // ETAPES DU CRUD
    // ETAPE 1 : CRÉATION AVEC LA ROUTE LIÉE AU FORMULAIRE
    #[Route("/admin/insert-book", name: "admin-insert-book")]
    public function insertBook(EntityManagerInterface $entityManager, Request $request)
    {
        $book = new Book();

        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($book);
            $entityManager->flush();
        }

        return $this->render('admin/form_book.html.twig', [
            'form' => $form->createView()
        ]);
    }

    // ETAPE 2: ETAPE DE LA LECTURE DE DONNES QUE CA SOIT EN LISTE OU EN INDIVIDUEL

    #[Route("/admin/books", name: "admin-books")]
    public function showBooks(BookRepository $bookRepository)
    {
        $books = $bookRepository->findAll();

        return $this->render('admin/listbooks.html.twig', [
            'books' => $books
        ]);
    }


    #[Route("/admin/book/{id}", name: "admin-book")]
    public function showBook(BookRepository $bookRepository, $id)
    {
        $book = $bookRepository->find($id);

        return $this->render('admin/book.html.twig', [
            'book' => $book
        ]);
    }

    //ETAPE 3 : UTILSISATION DE LA FONCTION REMOVE AFIN DE SUPPRIMER LA DONNEE SELECTIONNÉE
    #[Route("/admin/book/delete/{id}", name: "admin-book-delete")]
    public function deleteBook($id, BookRepository $bookRepository, EntityManagerInterface $entityManager){
        $book = $bookRepository->find($id);

        if(!is_null($book)) {
            // La fonctionnalité remove efface l'élément selectionné
            $entityManager->remove($book);
            $entityManager->flush();
            // Le redirect to route permet de rediriger vers la page précédent la suppression
            // Le Addflash permet d'afficher un message avertissant si l'opération à
            // été menée à bien ou non
        }

        return $this->redirectToRoute('admin-books');

    }

    //ETAPE 4 : UTILISATION DE LA PERSISTANCE AFIN DE METTRE À JOUR UNE DONNÉE SELECTIONNÉE
    #[Route("/admin/book/update/{id}", name: "admin-book-update")]
    public function updateBook($id, BookRepository $bookRepository, EntityManagerInterface $entityManager, Request $request)
    {
        $book = $bookRepository->find($id);

        // Création du formulaire en recuperant l'instance
        $form = $this->createForm(BookType::class, $book);
        //Renvoi du formulaire sur la page en twig via le biais de la fonction form

        // on donne à la variable qui contient le form
        // une instance de la classe request
        // pour que le form puisse récuperer toutes les données
        // des inputs et faire les setter automatiquement sur $category
        $form->handleRequest($request);

        // ici on note que si le contenu du formulaire est envoyé et est conforme
        // à ce qui est attendu en BDD, il sera pris en compte
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($book);
            $entityManager->flush();
        }

        return $this->render('admin/form_book.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route("/admin/books/search", name: "admin-books-search")]
    public function searchBook(Request $request, BookRepository $bookRepository)
    {
        // Je récupère les valeurs de mon formulaire dans ma route
        $search = $request->query->get('search');

        // je vais créer une méthode dans mon Repository
        // Qui permet de retrouver du contenu enn fonction d'un mot
        // entré dans la barre de recherche
        $books= $bookRepository->searchByWord($search);


        // Je renvoie un .twig en lui passant les articles trouvé
        // & les affiche
        return $this->render('admin/search_books.html.twig', [
            'books' => $books
        ]);
    }

}