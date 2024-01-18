<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\Comment;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // -------------------- Users ----------------------------
        $admin = new User();
        $admin->setEmail('admin@blog.fr')
            ->setPassword('$2y$13$sje5xwqUEuPg0IoYPI00MeI4ojRR.gobtIPYw/bHjUc7g9a3afM7G')
            ->setNickname('Administrateur')
            ->setRoles(['ROLE_ADMIN'])
            ->setCreatedAt($faker->dateTimeBetween('-12 months'));
        $manager->persist($admin);

        $users = [];
        for ($i=0; $i < 5; $i++) { 
            $user = new User();
            $user->setEmail('user'. $i .'@blog.fr')
            ->setPassword('$2y$13$sje5xwqUEuPg0IoYPI00MeI4ojRR.gobtIPYw/bHjUc7g9a3afM7G')
            ->setNickname($faker->userName)
            ->setRoles(['ROLE_USER'])
            ->setCreatedAt($faker->dateTimeBetween('-10months'));
            $manager->persist($user);
            array_push($users, $user);
        }

        $authors = [];
        for ($i=0; $i < 3; $i++) { 
            $author = new User();
            $author->setEmail('author'. $i .'@blog.fr')
            ->setPassword('$2y$13$sje5xwqUEuPg0IoYPI00MeI4ojRR.gobtIPYw/bHjUc7g9a3afM7G')
            ->setNickname($faker->userName)
            ->setRoles(['ROLE_AUTHOR'])
            ->setCreatedAt($faker->dateTimeBetween('-11 months'));
            $manager->persist($author);
            array_push($authors, $author);
        }

        // -------------------- Data for test --------------------
        $categories = array('Frontend', 'Backend', 'Fullstack', 'DevOps', 'Mobile', 'Data', 'Design');
        $catArray = [];
        foreach ($categories as $category) {
            $cat = new Category();
            $cat->setName($category)
                ->setImage('https://placehold.co/600x300/000000/FFF?text=' . $category);
            $manager->persist($cat);
            array_push($catArray, $cat);
        }

        for ($i=0; $i <= 100; $i++) { 
            $post = new Post();
            $post->setTitle($faker->text(50))
                ->setSlug($faker->slug)
                ->setContent($faker->text(1200))
                ->setImage('https://placehold.co/1280x300/000000/FFF?text=Article' . $i)
                ->setCreatedAt($faker->dateTimeBetween('-9 months'))
                ->setUpdatedAt($faker->dateTimeBetween('-6 months'))
                ->setAuthor($faker->randomElement($authors))
                ->setCategory($faker->randomElement($catArray))
                ->setIsPublished($faker->boolean(80));
            $manager->persist($post);

            // 3 to 5 comments for each post
            for ($j=0; $j < mt_rand(3, 5); $j++) { 
                $comment = new Comment();
                $comment->setAuthor($faker->randomElement($users))
                    ->setTitle($faker->text(50))
                    ->setContent($faker->text(250))
                    ->setCreatedAt($faker->dateTimeBetween('-9 months'))
                    ->setPost($post);
                $manager->persist($comment);
            }
        }

        // -------------------- Save data ------------------------
        $manager->flush();
    }
}
