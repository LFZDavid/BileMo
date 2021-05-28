<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\Product;
use App\Entity\Supplier;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{

    const BRANDS = ["Apple", "Samsung", "Huawei", "Sony", "Honor", "LG"];
    const SUPPLIERS_NAMES = ["Orange", "SFR", "Bouygues", "Free", "Axocom", "Extenso"];

    public function load(ObjectManager $manager)
    {

        /** Suppliers */
        foreach (self::SUPPLIERS_NAMES as $supplierName) {
            $supplier = new Supplier();
                $supplier->setName($supplierName)
                        ->setEmail(strtolower($supplierName).'@supplier.com')
                        ->setPwd($supplierName);

                for ($i=0; $i < rand(20,100); $i++) { 
                    $customer = new Customer();
                    $customer->setName('Cust '.substr($supplier->getName(),0,3). '-'.$i);
                    $supplier->addCustomer($customer);
                }
                $manager->persist($supplier);
        }

        /** Products */
        foreach (self::BRANDS as $brand) {
            for ($i=0; $i < rand(3,10); $i++) { 

                $product = new Product();
                $product->setBrand($brand)
                        ->setName($brand.'-'.$i)
                        ->setPrice(rand(4999,119999) / 100)
                        ->setStock(rand(0, 5000));

                $manager->persist($product);
            }
        }
        
        $manager->flush();
    }
}
