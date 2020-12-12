<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use App\Entity\User;
use Bezhanov\Faker\Provider\Commerce;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Liior\Faker\Prices;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture

{
    private $slug;
    private $encoder;
    public function __construct(SluggerInterface $slug, UserPasswordEncoderInterface $encoder)
    {
        $this->slug = $slug;
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new Prices($faker));
        $faker->addProvider(new Commerce($faker));

        $admin = new User();
        $hash =$this->encoder->encodePassword($admin, 'password');
        $admin
            ->setFullName('Admin')
            ->setEmail('Admin@gmail.com')
            ->setPassword($hash)
            ->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);
        $users = [];
        for ($u = 0; $u < 10; $u++ ){
            $user = new User();
            $hash =$this->encoder->encodePassword($user, 'password');

            $user
                ->setFullName($faker->name())
                ->setEmail($faker->email())
                ->setPassword($hash);
            $users[] = $user;
            $manager->persist($user);
        }
        $products = [];
        for($c = 0; $c < 3; $c++){
            $category = new Category;
            $category->setName($faker->department)
                ->setSlug(strTolower($this->slug->slug($category->getName())));
            $manager->persist($category);

            for($p = 0; $p < mt_rand(15, 20); $p++)
            {
                $product = new Product;
                $product
                    ->setName($faker->productName())
                    ->setPrice($faker->price(4000,20000))
                    ->setSlug(strTolower($this->slug->slug($product->getName())))
                    ->setCategory($category)
                    ->setDescription($faker->paragraph())
                    ->setPicture('https://picsum.photos/200/300');
                $products[] = $product;
                $manager->persist($product);
            }
        }

            for($pu = 0; $pu < mt_rand(20, 40); $pu++)
            {
                $purchase = new Purchase();
                $purchase
                    ->setFullName($faker->name)
                    ->setAddress($faker->streetAddress)
                    ->setPostalCode($faker->postcode)
                    ->setCity($faker->city)

                    ->setUser($faker->randomElement($users))
                    ->setTotal(mt_rand(2000, 5000))
                    ->setPurchasedAt($faker->dateTimeBetween('-6 months'));

                $selectedProduct = $faker->randomElements($products, mt_rand(3, 5));
                foreach ($selectedProduct as $product)
                {
                    $purchaseItem = new PurchaseItem();
                    $purchaseItem->setProduct($product)
                                ->setQuantity(mt_rand(1, 5))
                                ->setProductName($product->getName())
                                ->setProductPrice($product->getPrice())
                                ->setTotal(
                                    $purchaseItem->getProductPrice() * $purchaseItem->getQuantity()
                                )
                                ->setPurchase($purchase);
                        $manager->persist($purchaseItem);
                    }
                    if($faker->boolean(90))
                    {
                        $purchase->setStatus(Purchase::STATUS_PAID);
                    }
                $manager->persist($purchase);
            }

        $manager->flush();
    }
}