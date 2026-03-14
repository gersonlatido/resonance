<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ingredient;
use App\Models\Recipe;

class InventoryAndRecipesSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | INGREDIENT MASTER LIST
        |--------------------------------------------------------------------------
        | reorder_level = minimum stock before warning
        | stock_qty default = 0 so you fill it in UI later
        */

        $ingredients = [

            // ===== BASICS =====
            ['name'=>'Rice','unit'=>'g','reorder'=>3000],
            ['name'=>'Egg','unit'=>'pcs','reorder'=>30],
            ['name'=>'Garlic','unit'=>'g','reorder'=>500],
            ['name'=>'Onion','unit'=>'g','reorder'=>500],
            ['name'=>'Oil','unit'=>'ml','reorder'=>2000],
            ['name'=>'Butter','unit'=>'g','reorder'=>1000],
            ['name'=>'Salt','unit'=>'g','reorder'=>300],
            ['name'=>'Black Pepper','unit'=>'g','reorder'=>200],
            ['name'=>'Sugar','unit'=>'g','reorder'=>500],

            // ===== BREAKFAST PACKS =====
            ['name'=>'Beef Tapa Pack','unit'=>'g','reorder'=>2000],
            ['name'=>'Chicken Pastil Pack','unit'=>'g','reorder'=>1500],
            ['name'=>'Sisig Pack','unit'=>'g','reorder'=>1500],
            ['name'=>'Siomai','unit'=>'pcs','reorder'=>50],
            ['name'=>'Sausage','unit'=>'pcs','reorder'=>40],

            // ===== MAIN COURSES =====
            ['name'=>'Beef','unit'=>'g','reorder'=>2500],
            ['name'=>'Sirloin Beef','unit'=>'g','reorder'=>2000],
            ['name'=>'Soy Sauce','unit'=>'ml','reorder'=>1500],
            ['name'=>'Oyster Sauce','unit'=>'ml','reorder'=>800],
            ['name'=>'Worcestershire Sauce','unit'=>'ml','reorder'=>500],
            ['name'=>'Pork','unit'=>'g','reorder'=>3000],
            ['name'=>'Tomato','unit'=>'g','reorder'=>800],
            ['name'=>'Alamang','unit'=>'g','reorder'=>600],
            ['name'=>'Talong','unit'=>'g','reorder'=>1200],
            ['name'=>'Sitaw','unit'=>'g','reorder'=>1200],
            ['name'=>'Chili','unit'=>'g','reorder'=>300],
            ['name'=>'Peanut Paste','unit'=>'g','reorder'=>800],
            ['name'=>'Pork Stock','unit'=>'ml','reorder'=>1500],
            ['name'=>'Annatto','unit'=>'g','reorder'=>200],
            ['name'=>'Pechay','unit'=>'g','reorder'=>1000],

            // ===== PASTA =====
            ['name'=>'Pasta','unit'=>'g','reorder'=>2000],
            ['name'=>'Chicken Breast','unit'=>'g','reorder'=>2500],
            ['name'=>'Paprika','unit'=>'g','reorder'=>150],
            ['name'=>'Basil','unit'=>'g','reorder'=>150],
            ['name'=>'Peanuts','unit'=>'g','reorder'=>500],
            ['name'=>'All Purpose Cream','unit'=>'ml','reorder'=>1500],
            ['name'=>'Cheese','unit'=>'g','reorder'=>1200],
            ['name'=>'Olive Oil','unit'=>'ml','reorder'=>800],
            ['name'=>'Hotdog','unit'=>'g','reorder'=>1000],
            ['name'=>'Red Bell Pepper','unit'=>'g','reorder'=>500],
            ['name'=>'Tomato Sauce','unit'=>'ml','reorder'=>1500],
            ['name'=>'Ham','unit'=>'g','reorder'=>1200],
            ['name'=>'Bacon','unit'=>'g','reorder'=>1200],
            ['name'=>'Mushroom','unit'=>'g','reorder'=>800],
            ['name'=>'Cream of Mushroom','unit'=>'ml','reorder'=>1000],
            ['name'=>'Evap Milk','unit'=>'ml','reorder'=>1200],

            // ===== BURGER / CHICKEN PAGE INGREDIENTS =====
            ['name'=>'Chicken Wings','unit'=>'pcs','reorder'=>60],
            ['name'=>'Flour','unit'=>'g','reorder'=>2000],
            ['name'=>'Wings Sauce','unit'=>'ml','reorder'=>1200],
            ['name'=>'Breadcrumbs','unit'=>'g','reorder'=>1000],
            ['name'=>'Spice Mix','unit'=>'g','reorder'=>500],
            ['name'=>'Burger Bun','unit'=>'pcs','reorder'=>40],
            ['name'=>'Lettuce','unit'=>'g','reorder'=>500],
            ['name'=>'Mayonnaise','unit'=>'ml','reorder'=>1000],
            ['name'=>'Ketchup','unit'=>'ml','reorder'=>1000],

            // ===== FRAPPES =====
            ['name'=>'Cream Base','unit'=>'g','reorder'=>1500],
            ['name'=>'Ice','unit'=>'g','reorder'=>5000],
            ['name'=>'Water','unit'=>'ml','reorder'=>10000],
            ['name'=>'Whipped Cream','unit'=>'g','reorder'=>1200],
            ['name'=>'Oreo Powder','unit'=>'g','reorder'=>800],
            ['name'=>'Coffee Powder','unit'=>'g','reorder'=>800],
            ['name'=>'Seasalt Caramel Syrup','unit'=>'ml','reorder'=>800],

            // ===== COFFEE BASED =====
            ['name'=>'Fresh Milk','unit'=>'ml','reorder'=>4000],
            ['name'=>'Espresso','unit'=>'ml','reorder'=>1000],
            ['name'=>'Roasted Hazelnut Syrup','unit'=>'ml','reorder'=>600],
            ['name'=>'Cinnamon','unit'=>'g','reorder'=>150],
            ['name'=>'Caramel Sauce','unit'=>'ml','reorder'=>800],

            // ===== MILK BASED =====
            ['name'=>'Condensed Milk','unit'=>'ml','reorder'=>1200],
            ['name'=>'Cocoa Powder','unit'=>'g','reorder'=>800],
            ['name'=>'Biscoff Milk','unit'=>'ml','reorder'=>800],
            ['name'=>'Biscoff Spread','unit'=>'g','reorder'=>800],
            ['name'=>'Dark Chocolate Powder','unit'=>'g','reorder'=>600],
            ['name'=>'Milk Chocolate Powder','unit'=>'g','reorder'=>600],
            ['name'=>'Mallows','unit'=>'g','reorder'=>500],

            // ===== PIZZA PACKS =====
            ['name'=>'Pizza Pack - 6 Cheese','unit'=>'pcs','reorder'=>10],
            ['name'=>'Pizza Pack - Triple Pepperoni','unit'=>'pcs','reorder'=>10],
            ['name'=>'Pizza Pack - Ultimate Hawaiian','unit'=>'pcs','reorder'=>10],
            ['name'=>'Mini Pizza Pack - 5 Cheese','unit'=>'pcs','reorder'=>12],
            ['name'=>'Mini Pizza Pack - Pepperoni','unit'=>'pcs','reorder'=>12],
            ['name'=>'Mini Pizza Pack - Creamy Spinach','unit'=>'pcs','reorder'=>12],

            // ===== SNACK PACKS =====
            ['name'=>'Fries Pack','unit'=>'g','reorder'=>2000],
            ['name'=>'Chicken Poppers Pack','unit'=>'g','reorder'=>1500],
            ['name'=>'Cheesestick Pack','unit'=>'pcs','reorder'=>20],
        ];

        foreach ($ingredients as $ing) {
            Ingredient::updateOrCreate(
                ['name' => $ing['name']],
                [
                    'unit' => $ing['unit'],
                    'stock_qty' => 0,
                    'reorder_level' => $ing['reorder'],
                ]
            );
        }

        echo "Ingredients seeded with reorder levels ✔\n";
    }
}