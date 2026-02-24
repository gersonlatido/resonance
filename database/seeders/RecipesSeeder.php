<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Recipe;
use App\Models\Ingredient;

class RecipesSeeder extends Seeder
{
    public function run(): void
    {
        // Helper: get ingredient id by exact ingredient name
        $id = fn ($name) => Ingredient::where('name', $name)->value('id');

        // ✅ This is your menu → ingredient mapping
        $menus = [
            ['menu_id'=>'MENU001', 'lines'=>[
                ['Beef Tapa Pack',200], ['Rice',200], ['Egg',1], ['Garlic',5], ['Oil',10],
            ]],
            ['menu_id'=>'MENU002', 'lines'=>[
                ['Beef Tapa Pack',200], ['Rice',200], ['Egg',1], ['Garlic',5], ['Oil',10],
            ]],
            ['menu_id'=>'MENU003', 'lines'=>[
                ['Chicken Pastil Pack',150], ['Rice',200], ['Egg',1], ['Garlic',5], ['Oil',10],
            ]],
            ['menu_id'=>'MENU004', 'lines'=>[
                ['Sisig Pack',180], ['Rice',200], ['Egg',1], ['Onion',15], ['Oil',10],
            ]],
            ['menu_id'=>'MENU005', 'lines'=>[
                ['Siomai',5], ['Rice',200], ['Egg',1], ['Garlic',5], ['Oil',10],
            ]],
            ['menu_id'=>'MENU006', 'lines'=>[
                ['Sausage',2], ['Rice',200], ['Egg',1], ['Oil',10],
            ]],

            ['menu_id'=>'MENU007', 'lines'=>[
                ['Beef',180], ['Garlic',15], ['Butter',20], ['Oil',10],
                ['Soy Sauce',10], ['Oyster Sauce',10], ['Worcestershire Sauce',5], ['Black Pepper',2],
            ]],
            ['menu_id'=>'MENU008', 'lines'=>[
                ['Beef',150], ['Rice',200], ['Garlic',5], ['Butter',10], ['Soy Sauce',10], ['Oil',10],
            ]],
            ['menu_id'=>'MENU009', 'lines'=>[
                ['Rice',200], ['Garlic',10], ['Oil',10], ['Soy Sauce',5], ['Egg',1],
            ]],
            ['menu_id'=>'MENU010', 'lines'=>[
                ['Pork',180], ['Garlic',10], ['Onion',20], ['Tomato',25], ['Alamang',20],
                ['Talong',60], ['Sitaw',40], ['Sugar',5], ['Salt',2], ['Black Pepper',2], ['Chili',5],
            ]],
            ['menu_id'=>'MENU011', 'lines'=>[
                ['Sirloin Beef',180], ['Garlic',10], ['Soy Sauce',10], ['Sugar',5], ['Black Pepper',2], ['Oil',10],
            ]],
            ['menu_id'=>'MENU012', 'lines'=>[
                ['Pork',180], ['Peanut Paste',40], ['Pork Stock',200], ['Annatto',3], ['Black Pepper',2],
                ['Alamang',20], ['Pechay',60], ['Sitaw',50], ['Talong',70],
            ]],

            ['menu_id'=>'MENU013', 'lines'=>[
                ['Pasta',120], ['Chicken Breast',80], ['Garlic',10], ['Onion',20], ['Basil',10],
                ['Peanuts',10], ['All Purpose Cream',100], ['Butter',15], ['Cheese',30], ['Olive Oil',10],
                ['Paprika',2], ['Salt',2], ['Black Pepper',2], ['Sugar',3],
            ]],
            ['menu_id'=>'MENU014', 'lines'=>[
                ['Pasta',120], ['Hotdog',60], ['Garlic',10], ['Onion',20], ['Red Bell Pepper',20],
                ['Tomato Sauce',150], ['All Purpose Cream',40], ['Butter',10], ['Sugar',8], ['Salt',2], ['Black Pepper',2],
            ]],
            ['menu_id'=>'MENU015', 'lines'=>[
                ['Pasta',120], ['Bacon',50], ['Ham',50], ['Mushroom',40],
                ['Cream of Mushroom',80], ['All Purpose Cream',80], ['Evap Milk',40],
                ['Cheese',40], ['Garlic',10], ['Butter',15], ['Black Pepper',2],
            ]],

            ['menu_id'=>'MENU018', 'lines'=>[
                ['Chicken Wings',5], ['Flour',40], ['Oil',300], ['Wings Sauce',40], ['Salt',2], ['Black Pepper',2],
            ]],
            ['menu_id'=>'MENU019', 'lines'=>[
                ['Chicken Wings',3], ['Flour',25], ['Oil',200], ['Wings Sauce',30], ['Salt',2], ['Black Pepper',2],
            ]],
            ['menu_id'=>'MENU020', 'lines'=>[
                ['Chicken Breast',220], ['Flour',40], ['Egg',1], ['Breadcrumbs',40], ['Oil',350], ['Spice Mix',10],
            ]],

            ['menu_id'=>'MENU021', 'lines'=>[
                ['Oreo Powder',25], ['Cream Base',30], ['Water',120], ['Ice',200], ['Whipped Cream',25],
            ]],
            ['menu_id'=>'MENU022', 'lines'=>[
                ['Coffee Powder',20], ['Cream Base',30], ['Water',120], ['Ice',200], ['Whipped Cream',25],
            ]],
            ['menu_id'=>'MENU023', 'lines'=>[
                ['Seasalt Caramel Syrup',25], ['Cream Base',30], ['Water',120], ['Ice',200], ['Whipped Cream',25],
            ]],

            ['menu_id'=>'MENU024', 'lines'=>[
                ['Ice',180], ['Fresh Milk',180], ['Espresso',30], ['Roasted Hazelnut Syrup',20], ['Cinnamon',1],
            ]],
            ['menu_id'=>'MENU026', 'lines'=>[
                ['Ice',180], ['Fresh Milk',180], ['Espresso',30], ['Seasalt Caramel Syrup',20], ['Caramel Sauce',10],
            ]],

            ['menu_id'=>'MENU027', 'lines'=>[
                ['Fresh Milk',200], ['Cocoa Powder',20], ['Condensed Milk',20], ['Biscoff Milk',40], ['Ice',150],
            ]],
            ['menu_id'=>'MENU028', 'lines'=>[
                ['Fresh Milk',200], ['Biscoff Spread',25], ['Condensed Milk',20], ['Ice',150],
            ]],
            ['menu_id'=>'MENU029', 'lines'=>[
                ['Fresh Milk',200], ['Dark Chocolate Powder',15], ['Milk Chocolate Powder',15], ['Cocoa Powder',10],
                ['Condensed Milk',20], ['Mallows',20], ['Ice',150],
            ]],

            ['menu_id'=>'MENU030', 'lines'=>[['Pizza Pack - 6 Cheese',1]]],
            ['menu_id'=>'MENU031', 'lines'=>[['Pizza Pack - Triple Pepperoni',1]]],
            ['menu_id'=>'MENU032', 'lines'=>[['Pizza Pack - Ultimate Hawaiian',1]]],
            ['menu_id'=>'MENU033', 'lines'=>[['Mini Pizza Pack - 5 Cheese',1]]],
            ['menu_id'=>'MENU034', 'lines'=>[['Mini Pizza Pack - Pepperoni',1]]],
            ['menu_id'=>'MENU035', 'lines'=>[['Mini Pizza Pack - Creamy Spinach',1]]],

            ['menu_id'=>'MENU036', 'lines'=>[['Fries Pack',200]]],
            ['menu_id'=>'MENU037', 'lines'=>[['Chicken Poppers Pack',200]]],
            ['menu_id'=>'MENU038', 'lines'=>[['Cheesestick Pack',5]]],
        ];

        // ✅ Clear old recipes (optional, but recommended so you don’t duplicate)
        Recipe::truncate();

        $insert = [];

        foreach ($menus as $menu) {
            foreach ($menu['lines'] as $line) {
                [$ingredientName, $qtyNeeded] = $line;

                $ingredientId = $id($ingredientName);
                if (!$ingredientId) {
                    // skip if ingredient name not found in DB
                    continue;
                }

                $insert[] = [
                    'menu_id' => $menu['menu_id'],
                    'ingredient_id' => $ingredientId,
                    'qty_needed' => $qtyNeeded,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Insert only if there is data
        if (!empty($insert)) {
            Recipe::insert($insert);
        }
    }
}