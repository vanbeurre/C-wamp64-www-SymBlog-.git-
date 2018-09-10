<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CommentsRepository;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Articles;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use App\Entity\Users;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Comments;

class CommentsController extends AbstractController
{
    /**
     * @Route("/comments", name="comments")
     */
    public function index()
    {
        return $this->render('comments/index.html.twig', [
            'controller_name' => 'CommentsController',
        ]);
    }

      /**
     * @Route("/comments/{id}", name="displayComments" ,requirements={"id"="\d+"}))
     */
    public function displayOne(CommentsRepository $repository, $id){
        $comments = $repository->findBy(
            array('articles' => $id )
        );
        return $this->render('comments/displayComments.html.twig', [
            'comments' => $comments,
        ]);
    }
        /**
     * @Route("/comment/delete/{id}", name="deleteComment", requirements={"id"="\d+"})
     */
    public function deleteArticle(Comments $comments, ObjectManager $manager){
        $manager->remove( $comments );
        $manager->flush();
        return $this->redirectToRoute('detailArticles');
    }
}
