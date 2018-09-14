<?php

namespace App\Controller;

use App\Entity\Users;
use Symfony\Component\Form\Forms;
use App\Repository\UsersRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use App\Service\FileUploader;
use Symfony\Component\Translation\TranslatorInterface;

/**
* @Route("/{_locale}")
*     requirements={
*         "_locale": '%app.locales%',
*     }
*/

class UsersController extends AbstractController
{
    /**
     * @Route("/users", name="users")
     * @Route("/admin")
     * @Route("/profile")
     */

    public function index()
    {
        return $this->render('users/index.html.twig', [
            'controller_name' => 'UsersController',
            'test' =>'jojo bernard', 
        ]);
    }
     /**
     * @Route("/user/users/{id}", name="displayUser" ,requirements={"id"="\d+"}))
     */
    public function displayOne(UsersRepository $repository, $id,TranslatorInterface $translator){
        // $translated = $translator->trans('hello');
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
     * @Route("/admin/users/addUser", name="addUser")
     * @Route("/user/users/edit/{id}", name="editUser", requirements={"id"="\d+"})
     */

    public function addUser(Users $user=null, Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder, FileUploader $fileUploader){

        if(is_null($user)){
            $user=new Users();
            $form = $this->createFormBuilder($user, array('allow_extra_fields' =>true))
            ->add('name', TextType::class)
                    ->add('lastName', TextType::class)
                    ->add('email', TextType::class)
                    ->add('username', TextType::class)
                    ->add('role', ChoiceType::class, array(
                        'choices'  => array(
                            'Utilisateur' => 'ROLE_USER',
                            'Auteur' => 'ROLE_AUTHOR',
                            'Admin' => 'ROLE_ADMIN',
                        ),
                        'mapped' => false
                    ))      
                     ->add('password', RepeatedType::class, array(
                        'type' => PasswordType::class,
                        'invalid_message' => 'The password fields must match.',
                        'options' => array('attr' => array('class' => 'password-field')),
                        'required' => true,
                        'first_options'  => array('label' => 'Password'),
                        'second_options' => array('label' => 'Repeat Password'),
                    ))
                    ->add('avatar', FileType::class, array('label' => 'Avatar (PNG file)', "data_class" => null))
                    ->getForm();
                    /////////////////////////////////////////////////////////////////////////////////////////////////
                     }else{
                        $form = $this->createFormBuilder($user, array('allow_extra_fields' =>true))
                        ->add('name', TextType::class)
                                ->add('lastName', TextType::class)
                                ->add('email', TextType::class)
                                ->add('username', TextType::class)
                                ->add('role', ChoiceType::class, array(
                                    'choices'  => array(
                                        'Utilisateur' => 'ROLE_USER',
                                        'Auteur' => 'ROLE_AUTHOR',
                                        'Admin' => 'ROLE_ADMIN',
                                    ),
                                    'mapped' => false
                                )) ->add('avatar', FileType::class, array('label' => 'Avatar (PNG file)', "data_class" => null))
                                ->getForm();
                           
                             $form->handleRequest($request);    
                             if ( $form->isSubmitted() && $form->isValid() && !is_null($user) ) {         
                                // $file stores the uploaded PNG file
                                /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
                                $file = $form['avatar']->getData();
                                $fileName = $fileUploader->upload($file);       
                                $user->setAvatar($fileName);
                                dump($file); 
                                $user->setRoles($request->request->all()['form']['role']); //Com fabien : WTF ??
                                $user->setDateCreate( new \DateTime() ); 
                                $manager->persist( $user );
                                $manager->flush();
                            
                                return $this->redirectToRoute('listUsers');
                              
                        }
                        return $this->render('users/addUser.html.twig',[
                            'form'=>$form->createView()
                        ]);             
                     }
                  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////            
            $form->handleRequest($request);
            dump($user);
            if ( $form->isSubmitted() && $form->isValid() && !is_null($user) ) {         
                    // $file stores the uploaded PNG file
                    /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
                    $file = $form['avatar']->getData();
                    $fileName = $fileUploader->upload($file);       
                    $user->setAvatar($fileName);
                    dump($file); 
                    $user->setRoles($request->request->all()['form']['role']); //Com fabien : WTF ??
                    $user->setDateCreate( new \DateTime() ); 
                    $plainPassword= $user->getPassword(); 
                    $encoded = $encoder->encodePassword($user, $plainPassword);
                    $user->setPassword($encoded);
                    $manager->persist( $user );
                    $manager->flush();
                
                    return $this->redirectToRoute('listUsers');
                  
            }
            return $this->render('users/addUser.html.twig',[
                'form'=>$form->createView()
            ]);   
    }
    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }
    /**
     * @Route("/admin/users/delete/{id}", name="deleteUser", requirements={"id"="\d+"})
     */
    public function deleteUser(Users $user, ObjectManager $manager){
        $manager->remove( $user );
        $manager->flush();
        return $this->redirectToRoute('listUsers');
    }

  

    
  
}
