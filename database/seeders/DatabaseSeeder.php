<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\Restaurant;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            CitySeeder::class,
            UserSeeder::class,
        ]);

        $this->seedDemoRestaurants();
    }

    public function seedDemoRestaurants()
    {
        $products   = Product::factory(7);
        $categories = Category::factory(5)->has($products);
        $staffMember = User::factory()->staff();
        $restaurant  = Restaurant::factory()->has($categories)->has($staffMember, 'staff');

        User::factory(50)->vendor()->has($restaurant)->create();
    }
}
