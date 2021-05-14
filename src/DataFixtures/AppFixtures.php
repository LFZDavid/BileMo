<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{

    const BRANDS = ["Apple", "Samsung", "Huawei", "Sony", "Honor", "LG"];

    public function load(ObjectManager $manager)
    {

        foreach (self::BRANDS as $brand) {
            for ($i=0; $i < rand(3,10); $i++) { 

                $product = new Product();
                $product->setBrand($brand)
                        ->setName($brand.'-'.$i)
                        ->setPrice(rand(49,1199) + (rand( 0, 99 ) / 100))
                        ->setStock(rand(0, 5000));

                $manager->persist($product);
            }
        }
        
        $manager->flush();
    }
}
