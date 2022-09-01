<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Monolog\DateTimeImmutable;
use App\Entity\User;
use App\Entity\News;
use App\Entity\Category;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setUsername('Admin');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword('$2y$13$UU7acjHASOPWMVZWab442usroUsr5LLCCObuq6fEOXqfOXi4Jps/e');
        $manager->persist($user);

        for($i=0; $i<20; $i++ ){
            $user = new User();
            $user->setUsername('user'.$i+1);
            $user->setRoles(['ROLE_USER']);
            $user->setPassword('$2y$13$UU7acjHASOPWMVZWab442usroUsr5LLCCObuq6fEOXqfOXi4Jps/e');
            $manager->persist($user);
        }

        for($i=0; $i<10; $i++ ){
            $category = new Category();
            $category->setLabel('cat'.$i+1);
            $manager->persist($category);
        } 

        for($i=0; $i<10; $i++){
            $date = new DateTimeImmutable('2022-07-12');
            $news = new News();
            $news->setTitle('Titre'.$i+1);
            $news->setDescriptionShort('Description courte'.$i+1);
            $news->setDescriptionLong('Description longue'.$i+1);
            $news->setReleaseDate($date);
            $manager->persist($news);
        }

        $manager->flush();
    }

}
