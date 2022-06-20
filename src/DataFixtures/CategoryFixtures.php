<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class CategoryFixtures extends Fixture implements DependentFixtureInterface
{
    private Generator $faker;

    public function __construct(){
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        $count = $this->faker->numberBetween(10, 20);
        for($i = 1; $i <= $count; $i++){
            $this->createCategory($manager);
        }
        $manager->flush();
    }

    public function createCategory(ObjectManager $manager): Category
    {
        static $total = 0;
        $category = new Category();
        $category->setTitle($this->faker->sentence(3));
        $manager->persist($category);
        $this->setReference('category-' . $total++, $category);

        return $category;
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
