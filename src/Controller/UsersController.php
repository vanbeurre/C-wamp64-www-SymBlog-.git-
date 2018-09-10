<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UsersRepository;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Users;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;


class UsersController extends AbstractController
{
    /**
     * @Route("/users", name="users")
     */
    public function index()
    {
        return $this->render('users/index.html.twig', [
            'controller_name' => 'UsersController',
            'test' =>'jojo bernard', 
        ]);
    }
     /**
     * @Route("/users/{id}", name="displayUser" ,requirements={"id"="\d+"}))
     */
    public function displayOne(UsersRepository $repository, $id){
        $user = $repository->findOneById($id); 
        return $this->render('users/usersDetail.html.twig', [
            'user' => $user,
        ]);
    }
      /**
     * @Route("/users/liste", name="listUsers")
     */
    public function list(UsersRepository $repository){
  
        // $usersArray = $repository->findOneById(6); 
        $usersArray = $repository->findAll(); 
        
        // var_dump($usersArray);
        return $this->render('users/listUsers.html.twig', [
            'users' => $usersArray,
        ]);
    }
   /**
     * @Route("/users/addUser", name="addUser")
     * @Route("/users/edit/{id}", name="editUser", requirements={"id"="\d+"})
     */

    public function addUser(Users $user=null, Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder){

        if(is_null($user))
            $user=new Users();

            // $user->setName('name')
            //      ->setName('lastname')
            //      ->setName('password')
            //      ->setName('email'); 
    
            $form = $this->createFormBuilder($user)
                    ->add('name', TextType::class)
                    ->add('lastName', TextType::class)
                    ->add('password', RepeatedType::class, array(
                        'type' => PasswordType::class,
                        'invalid_message' => 'The password fields must match.',
                        'options' => array('attr' => array('class' => 'password-field')),
                        'required' => true,
                        'first_options'  => array('label' => 'Password'),
                        'second_options' => array('label' => 'Repeat Password'),
                    ))
                    ->add('email', TextType::class)
                    ->getForm();
                 
            $form->handleRequest($request);
            dump($user);
            if ( $form->isSubmitted() && $form->isValid() ) {
                
                    $user->setDateCreate( new \DateTime() ); 
                    $plainPassword= $user->getPassword(); 
                    $encoded = $encoder->encodePassword($user, $plainPassword);
                    $user->setPassword($encoded);
                    $manager->persist( $user );
                    $manager->flush();
                
                    return $this->redirectToRoute('listUsers');
                  
            }
            return $this->render('users/addUser.html.twig',[
                'form'=>$form->createView(),
            ]);   
    }
    /**
     * @Route("/users/delete/{id}", name="deleteUser", requirements={"id"="\d+"})
     */
    public function deleteUser(Users $user, ObjectManager $manager){
        $manager->remove( $user );
        $manager->flush();
        return $this->redirectToRoute('listUsers');
    }

    
  
}
