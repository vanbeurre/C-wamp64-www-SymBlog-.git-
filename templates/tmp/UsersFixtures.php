<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Users;

class UsersFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
       
        for( $i=0 ; $i<5 ; $i++ ){
           $users = new Users(); 
           $users->setName($i."ben")
                ->setLastName($i."vanhau")
                ->setEmail("ttt@mail.fr")
                ->setPassword($i)
                ->setDateCreate(new \DateTime())
                ->setDateLastLogin(new \DateTime());

           $manager->persist($users);
        }
        $manager->flush();
    }
}
