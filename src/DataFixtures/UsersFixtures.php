<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Users;
use App\Entity\Articles;
use App\Entity\Comments;
use App\Entity\Categories;

class UsersFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for( $i=0 ; $i<5 ; $i++ ){
            $users = new Users(); 
            $users->setName($i."ben")
                 ->setLastName($i."vanhau")
                 ->setPassword($i)
                 ->setEmail("ttt@mail.fr")
                 ->setDateCreate(new \DateTime())
                 ->setDateLastLogin(new \DateTime())
                 ->setUserName($i."vanhau"); 
                
                 for ( $j=0; $j<1; $j++){
                     $articles = new Articles(); 
                     $articles->setTitle("title")
                            ->setContent("content")
                            ->setUser($users)
                            ->setDateCreate(new \DateTime());

                            $this->setReference('art-article', $articles); 
                            $manager->persist($articles);

                            for( $k=0; $k<1; $k++){
                                $comments = new Comments();
                                $comments->setContent('yoyoyo')
                                        ->setUsers($users)
                                         ->setArticles($articles)
                                        ->setDateCreate(new \DateTime());
                                $categories = new Categories(); 
                                $categories->setName('jeux video')
                                            ->addArticle($this->getReference('art-article')); 
                                        
                                $manager->persist($categories); 
                                $manager->persist($comments);
                            }
                 }
            $manager->persist($users);
                  
         }

        $manager->flush();
    }
}
