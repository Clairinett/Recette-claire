<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Faker\Generator;
use App\Entity\Recette;
use App\Entity\Ingredient;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    private Generator $faker;

        public function __construct()
        {
            $this->faker = Factory::create('fr_FR');
        }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        // Ingredient
        $ingredients = [];
        for ($i = 0; $i < 50; $i++){
            $ingredient = new Ingredient();
            $ingredient->setName($this->faker->word())
                ->setPrice(mt_rand(0, 100));
            
            $ingredients[] = $ingredient;
            $manager->persist($ingredient);
        }

        // Recette
        for ($j = 0; $j < 25; $j++){
            $recette = new Recette();
            $recette->setName($this->faker->word())
                ->setTime(mt_rand(0, 1) == 1 ? mt_rand(1, 1440) : null)
                ->setNbPeople(mt_rand(0, 1) == 1 ? mt_rand(1, 50) : null)
                ->setDifficulty(mt_rand(0, 1) == 1 ? mt_rand(1, 5) : null)
                ->setDescription($this->faker->text(300))
                ->setPrice(mt_rand(0, 1) == 1 ? mt_rand(1, 1000) : null)
                ->setIsFavorite(mt_rand(0, 1) == 1 ? true : false);

            for ($k = 0; $k < mt_rand(5, 15); $k++){
                $recette->addIngredient($ingredients[mt_rand(0, count($ingredients) - 1)]);
            }

            $manager->persist($recette);
        }

        // User
        for ($u = 0; $u < 10; $u++) {
            $user = new User();
            $user->setFullName($this->faker->name())
                ->setPseudo(mt_rand(0, 1) === 1 ? $this->faker->firstName() : null)
                ->setEmail($this->faker->email())
                ->setRoles(['ROLE_USER'])
                ->setPlainPassword('password');

            $manager->persist($user);
        }
        
        $manager->flush();
    }
}
