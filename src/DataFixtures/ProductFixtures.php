<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Stock;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public const PRODUCTS_DATA = [
        [
            'name' => 'Blackbelt',
            'price' => 29.90,
            'image' => 'blackbelt.jpg',
            'featured' => true,
        ],
        [
            'name' => 'BlueBelt',
            'price' => 29.90,
            'image' => 'bluebelt.jpg',
            'featured' => false,
        ],
        [
            'name' => 'Street',
            'price' => 34.50,
            'image' => 'street.jpg',
            'featured' => false,
        ],
        [
            'name' => 'Pokeball',
            'price' => 45.00,
            'image' => 'pokeball.jpg',
            'featured' => true,
        ],
        [
            'name' => 'PinkLady',
            'price' => 29.90,
            'image' => 'pinklady.jpg',
            'featured' => false,
        ],
        [
            'name' => 'Snow',
            'price' => 32.00,
            'image' => 'snow.jpg',
            'featured' => false,
        ],
        [
            'name' => 'Greyback',
            'price' => 28.50,
            'image' => 'greyback.jpg',
            'featured' => false,
        ],
        [
            'name' => 'BlueCloud',
            'price' => 45.00,
            'image' => 'bluecloud.jpg',
            'featured' => false,
        ],
        [
            'name' => 'BornInUsa',
            'price' => 59.90,
            'image' => 'borninusa.jpg',
            'featured' => true,
        ],
        [
            'name' => 'GreenSchool',
            'price' => 42.20,
            'image' => 'greenschool.jpg',
            'featured' => false,
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::PRODUCTS_DATA as $data) {
            $product = new Product();
            $product->setName($data['name']);
            $product->setPrice((string) $data['price']);
            $product->setImage($data['image']);
            $product->setFeatured($data['featured']);

            foreach (Stock::SIZES as $size) {
                $stock = new Stock();
                $stock->setProduct($product);
                $stock->setSize($size);
                $stock->setQuantity(rand(2, 10));

                $product->addStock($stock);
                $manager->persist($stock);
            }

            $manager->persist($product);
        }

        $manager->flush();
    }
}
