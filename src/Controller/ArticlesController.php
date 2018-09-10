<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ArticlesRepository;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Articles;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use App\Entity\Users;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Comments;



class ArticlesController extends AbstractController
{
    /**
     * @Route("/articles", name="articles")
     */
    public function index()
    {
        return $this->render('articles/index.html.twig', [
            'controller_name' => 'ArticlesController',
        ]);
    }

    /**
     * @Route("/articles/liste", name="listArticles")
     */
    public function list(ArticlesRepository $repository){
  
        // $usersArray = $repository->findOneById(6); 
        $articlesArray = $repository->findAll(); 
        
        // var_dump($usersArray);
        return $this->render('Articles/listArticles.html.twig', [
            'articles' => $articlesArray,
        ]);
    }

      /**
     * @Route("/articles/{id}", name="displayArticle" ,requirements={"id"="\d+"}))
     */
    public function displayOne(Comments $comments=null, ArticlesRepository $repository, $id,  Request $request, ObjectManager $manager){
        $articles = $repository->findOneById($id);

        if(is_null($comments))
        $comments=new Comments();

        $form = $this->createFormBuilder($comments)
                ->add('content', TextareaType::class)
                ->add('users', EntityType::class, array(
                    // looks for choices from this entity
                    'class' => Users::class,
                    'choice_label' => 'name'
                    )
                )
                ->getForm();
                 
                $form->handleRequest($request);
                dump($comments);
                if ( $form->isSubmitted() && $form->isValid() ) {
                        $comments->setArticles($articles); 
                        $comments->setDateCreate( new \DateTime() ); 
                        $manager->persist( $comments );
                        $manager->flush();
                    
                        return $this->redirectToRoute('listArticles');
                      
                }
                 
        return $this->render('articles/detailArticles.html.twig', [
            'articles' => $articles,
            'form'=>$form->createView(),
        ]);
    }

     /**
     * @Route("/articles/addArticle", name="addArticle")
     * @Route("/articles/editArticle/{id}", name="editArticle", requirements={"id"="\d+"})
     */

    public function addArticle(Articles $article=null, Request $request, ObjectManager $manager){

        if(is_null($article))
            $article=new Articles();

            $form = $this->createFormBuilder($article)
                    ->add('title', TextType::class)
                    ->add('content', TextareaType::class)
                    ->add('user', EntityType::class, array(
                        // looks for choices from this entity
                        'class' => Users::class,
                        'choice_label' => 'name'
                        )
                    )
                    ->getForm();
                 
            $form->handleRequest($request);
            dump($article);
            if ( $form->isSubmitted() && $form->isValid() ) {
                
                    $article->setDateCreate( new \DateTime() ); 
                    $manager->persist( $article );
                    $manager->flush();
                
                    return $this->redirectToRoute('listArticles');
                  
            }
            return $this->render('articles/addArticles.html.twig',[
                'form'=>$form->createView(),
            ]);   
    }
        /**
     * @Route("/article/delete/{id}", name="deleteArticle", requirements={"id"="\d+"})
     */
    public function deleteArticle(Articles $article, ObjectManager $manager){
        $manager->remove( $article );
        $manager->flush();
        return $this->redirectToRoute('listArticles');
    }
}
