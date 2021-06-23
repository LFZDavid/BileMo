<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\Product;
use App\Entity\Supplier;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    const BRANDS = ["Apple", "Samsung", "Huawei", "Sony", "Honor", "LG"];
    const SUPPLIERS_NAMES = ["Orange", "SFR", "Bouygues", "Free", "Axocom", "Extenso"];
    const TEST_CUSTOMERS = ["find", "already_exist", "delete"];

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {

        /** Suppliers */
        foreach (self::SUPPLIERS_NAMES as $supplierName) {
            $supplier = new Supplier();
                $supplier->setName($supplierName)
                        ->setEmail(strtolower($supplierName).'@supplier.com')
                        ->setPwd($this->passwordEncoder->encodePassword($supplier,$supplierName));

                for ($i=0; $i < 50; $i++) { 
                    $customer = new Customer();
                    $customer->setName('Cust '.substr($supplier->getName(),0,3). '-'.$i);
                    $supplier->addCustomer($customer);
                }
                $manager->persist($supplier);
        }

        /** Products */
        foreach (self::BRANDS as $brand) {
            for ($i=0; $i < 8; $i++) { 

                $product = new Product();
                $product->setBrand($brand)
                        ->setName($brand.'-'.$i)
                        ->setPrice(($i * 1234)/ 100)
                        ->setStock((234));

                $manager->persist($product);
            }
        }
        
        /** Test fixtures*/
        $this->loadTestFixtures($manager);

        $manager->flush();
    }

    protected function loadTestFixtures(ObjectManager $manager) {

        /** Products */
        $product = new Product();
        $product->setName('find')
                ->setBrand('Test')
                ->setPrice(0)
                ->setStock(0);
        $manager->persist($product);
        
        /** Suppliers */
        $supplier = new Supplier();
        $supplier->setName('SupplierTest')
                ->setEmail('supplier@test.com')
                ->setPwd($this->passwordEncoder->encodePassword($supplier,'pwdtest'));
                

        /** Customer */
        foreach (self::TEST_CUSTOMERS as $custName) {
            $customer = new Customer();
            $customer->setName($custName);
            $supplier->addCustomer($customer);
        }
        $manager->persist($supplier);

        /** Other Supplier and Customer */
        $otherSupplier = new Supplier();
        $otherSupplier->setName('OtherSupplier')
                    ->setEmail('other@test.com')
                    ->setPwd($this->passwordEncoder->encodePassword($supplier,'pwdtest'));
        $otherCustomer = new Customer();
        $otherCustomer->setName('otherSupplierCustomer');
        $otherSupplier->addCustomer($otherCustomer);
        $manager->persist($otherSupplier);
        
    }
}
