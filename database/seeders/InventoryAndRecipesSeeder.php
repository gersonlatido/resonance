<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ingredient;
use App\Models\Recipe;

class InventoryAndRecipesSeeder extends Seeder
{
    public function run(): void
    {
        // =========================
        // 1) INGREDIENT MASTER LIST
        // =========================
        $ingredients = [
            // Basics
            ['name'=>'Rice','unit'=>'g'],
            ['name'=>'Egg','unit'=>'pcs'],
            ['name'=>'Garlic','unit'=>'g'],
            ['name'=>'Onion','unit'=>'g'],
            ['name'=>'Oil','unit'=>'ml'],
            ['name'=>'Butter','unit'=>'g'],
            ['name'=>'Salt','unit'=>'g'],
            ['name'=>'Black Pepper','unit'=>'g'],
            ['name'=>'Sugar','unit'=>'g'],

            // Breakfast packs
            ['name'=>'Beef Tapa Pack','unit'=>'g'],
            ['name'=>'Chicken Pastil Pack','unit'=>'g'],
            ['name'=>'Sisig Pack','unit'=>'g'],
            ['name'=>'Siomai','unit'=>'pcs'],
            ['name'=>'Sausage','unit'=>'pcs'],

            // Main courses
            ['name'=>'Beef','unit'=>'g'],
            ['name'=>'Sirloin Beef','unit'=>'g'],
            ['name'=>'Soy Sauce','unit'=>'ml'],
            ['name'=>'Oyster Sauce','unit'=>'ml'],
            ['name'=>'Worcestershire Sauce','unit'=>'ml'],
            ['name'=>'Pork','unit'=>'g'],
            ['name'=>'Tomato','unit'=>'g'],
            ['name'=>'Alamang','unit'=>'g'],
            ['name'=>'Talong','unit'=>'g'],
            ['name'=>'Sitaw','unit'=>'g'],
            ['name'=>'Chili','unit'=>'g'],
            ['name'=>'Peanut Paste','unit'=>'g'],
            ['name'=>'Pork Stock','unit'=>'ml'],
            ['name'=>'Annatto','unit'=>'g'],
            ['name'=>'Pechay','unit'=>'g'],

            // Pasta
            ['name'=>'Pasta','unit'=>'g'],
            ['name'=>'Chicken Breast','unit'=>'g'],
            ['name'=>'Paprika','unit'=>'g'],
            ['name'=>'Basil','unit'=>'g'],
            ['name'=>'Peanuts','unit'=>'g'],
            ['name'=>'All Purpose Cream','unit'=>'ml'],
            ['name'=>'Cheese','unit'=>'g'],
            ['name'=>'Olive Oil','unit'=>'ml'],
            ['name'=>'Hotdog','unit'=>'g'],
            ['name'=>'Red Bell Pepper','unit'=>'g'],
            ['name'=>'Tomato Sauce','unit'=>'ml'],
            ['name'=>'Ham','unit'=>'g'],
            ['name'=>'Bacon','unit'=>'g'],
            ['name'=>'Mushroom','unit'=>'g'],
            ['name'=>'Cream of Mushroom','unit'=>'ml'],
            ['name'=>'Evap Milk','unit'=>'ml'],

            // Chicken menu
            ['name'=>'Chicken Wings','unit'=>'pcs'],
            ['name'=>'Flour','unit'=>'g'],
            ['name'=>'Wings Sauce','unit'=>'ml'],
            ['name'=>'Breadcrumbs','unit'=>'g'],
            ['name'=>'Spice Mix','unit'=>'g'],

            // Frappes
            ['name'=>'Cream Base','unit'=>'g'],
            ['name'=>'Ice','unit'=>'g'],
            ['name'=>'Water','unit'=>'ml'],
            ['name'=>'Whipped Cream','unit'=>'g'],
            ['name'=>'Oreo Powder','unit'=>'g'],
            ['name'=>'Coffee Powder','unit'=>'g'],
            ['name'=>'Seasalt Caramel Syrup','unit'=>'ml'],

            // Coffee based
            ['name'=>'Fresh Milk','unit'=>'ml'],
            ['name'=>'Espresso','unit'=>'ml'],
            ['name'=>'Roasted Hazelnut Syrup','unit'=>'ml'],
            ['name'=>'Cinnamon','unit'=>'g'],
            ['name'=>'Caramel Sauce','unit'=>'ml'],

            // Milk based
            ['name'=>'Condensed Milk','unit'=>'ml'],
            ['name'=>'Cocoa Powder','unit'=>'g'],
            ['name'=>'Biscoff Milk','unit'=>'ml'],
            ['name'=>'Biscoff Spread','unit'=>'g'],
            ['name'=>'Dark Chocolate Powder','unit'=>'g'],
            ['name'=>'Milk Chocolate Powder','unit'=>'g'],
            ['name'=>'Mallows','unit'=>'g'],

            // Pizza packs
            ['name'=>'Pizza Pack - 6 Cheese','unit'=>'pcs'],
            ['name'=>'Pizza Pack - Triple Pepperoni','unit'=>'pcs'],
            ['name'=>'Pizza Pack - Ultimate Hawaiian','unit'=>'pcs'],
            ['name'=>'Mini Pizza Pack - 5 Cheese','unit'=>'pcs'],
            ['name'=>'Mini Pizza Pack - Pepperoni','unit'=>'pcs'],
            ['name'=>'Mini Pizza Pack - Creamy Spinach','unit'=>'pcs'],

            // Snacks packs
            ['name'=>'Fries Pack','unit'=>'g'],
            ['name'=>'Chicken Poppers Pack','unit'=>'g'],
            ['name'=>'Cheesestick Pack','unit'=>'pcs'],
        ];

        foreach ($ingredients as $ing) {
            Ingredient::updateOrCreate(
                ['name' => $ing['name']],
                [
                    'unit' => $ing['unit'],
                    'stock_qty' => 0,
                    'reorder_level' => 0,
                ]
            );
        }

        $ids = Ingredient::pluck('id', 'name')->toArray();

        // =========================
        // 2) RECIPES (BOM)
        // =========================
        $recipes = [
            ['menu_id'=>'MENU001', 'lines'=>[
                ['Beef Tapa Pack',200,'g'], ['Rice',200,'g'], ['Egg',1,'pcs'], ['Garlic',5,'g'], ['Oil',10,'ml'],
            ]],
            ['menu_id'=>'MENU002', 'lines'=>[
                ['Beef Tapa Pack',200,'g'], ['Rice',200,'g'], ['Egg',1,'pcs'], ['Garlic',5,'g'], ['Oil',10,'ml'],
            ]],
            ['menu_id'=>'MENU003', 'lines'=>[
                ['Chicken Pastil Pack',150,'g'], ['Rice',200,'g'], ['Egg',1,'pcs'], ['Garlic',5,'g'], ['Oil',10,'ml'],
            ]],
            ['menu_id'=>'MENU004', 'lines'=>[
                ['Sisig Pack',180,'g'], ['Rice',200,'g'], ['Egg',1,'pcs'], ['Onion',15,'g'], ['Oil',10,'ml'],
            ]],
            ['menu_id'=>'MENU005', 'lines'=>[
                ['Siomai',5,'pcs'], ['Rice',200,'g'], ['Egg',1,'pcs'], ['Garlic',5,'g'], ['Oil',10,'ml'],
            ]],
            ['menu_id'=>'MENU006', 'lines'=>[
                ['Sausage',2,'pcs'], ['Rice',200,'g'], ['Egg',1,'pcs'], ['Oil',10,'ml'],
            ]],

            ['menu_id'=>'MENU007', 'lines'=>[
                ['Beef',180,'g'], ['Garlic',15,'g'], ['Butter',20,'g'], ['Oil',10,'ml'],
                ['Soy Sauce',10,'ml'], ['Oyster Sauce',10,'ml'], ['Worcestershire Sauce',5,'ml'], ['Black Pepper',2,'g'],
            ]],
            ['menu_id'=>'MENU008', 'lines'=>[
                ['Beef',150,'g'], ['Rice',200,'g'], ['Garlic',5,'g'], ['Butter',10,'g'], ['Soy Sauce',10,'ml'], ['Oil',10,'ml'],
            ]],
            ['menu_id'=>'MENU009', 'lines'=>[
                ['Rice',200,'g'], ['Garlic',10,'g'], ['Oil',10,'ml'], ['Soy Sauce',5,'ml'], ['Egg',1,'pcs'],
            ]],
            ['menu_id'=>'MENU010', 'lines'=>[
                ['Pork',180,'g'], ['Garlic',10,'g'], ['Onion',20,'g'], ['Tomato',25,'g'], ['Alamang',20,'g'],
                ['Talong',60,'g'], ['Sitaw',40,'g'], ['Sugar',5,'g'], ['Salt',2,'g'], ['Black Pepper',2,'g'], ['Chili',5,'g'],
            ]],
            ['menu_id'=>'MENU011', 'lines'=>[
                ['Sirloin Beef',180,'g'], ['Garlic',10,'g'], ['Soy Sauce',10,'ml'], ['Sugar',5,'g'], ['Black Pepper',2,'g'], ['Oil',10,'ml'],
            ]],
            ['menu_id'=>'MENU012', 'lines'=>[
                ['Pork',180,'g'], ['Peanut Paste',40,'g'], ['Pork Stock',200,'ml'], ['Annatto',3,'g'], ['Black Pepper',2,'g'],
                ['Alamang',20,'g'], ['Pechay',60,'g'], ['Sitaw',50,'g'], ['Talong',70,'g'],
            ]],

            ['menu_id'=>'MENU013', 'lines'=>[
                ['Pasta',120,'g'], ['Chicken Breast',80,'g'], ['Garlic',10,'g'], ['Onion',20,'g'], ['Basil',10,'g'],
                ['Peanuts',10,'g'], ['All Purpose Cream',100,'ml'], ['Butter',15,'g'], ['Cheese',30,'g'], ['Olive Oil',10,'ml'],
                ['Paprika',2,'g'], ['Salt',2,'g'], ['Black Pepper',2,'g'], ['Sugar',3,'g'],
            ]],
            ['menu_id'=>'MENU014', 'lines'=>[
                ['Pasta',120,'g'], ['Hotdog',60,'g'], ['Garlic',10,'g'], ['Onion',20,'g'], ['Red Bell Pepper',20,'g'],
                ['Tomato Sauce',150,'ml'], ['All Purpose Cream',40,'ml'], ['Butter',10,'g'], ['Sugar',8,'g'], ['Salt',2,'g'], ['Black Pepper',2,'g'],
            ]],
            ['menu_id'=>'MENU015', 'lines'=>[
                ['Pasta',120,'g'], ['Bacon',50,'g'], ['Ham',50,'g'], ['Mushroom',40,'g'],
                ['Cream of Mushroom',80,'ml'], ['All Purpose Cream',80,'ml'], ['Evap Milk',40,'ml'],
                ['Cheese',40,'g'], ['Garlic',10,'g'], ['Butter',15,'g'], ['Black Pepper',2,'g'],
            ]],

            ['menu_id'=>'MENU018', 'lines'=>[
                ['Chicken Wings',5,'pcs'], ['Flour',40,'g'], ['Oil',300,'ml'], ['Wings Sauce',40,'ml'], ['Salt',2,'g'], ['Black Pepper',2,'g'],
            ]],
            ['menu_id'=>'MENU019', 'lines'=>[
                ['Chicken Wings',3,'pcs'], ['Flour',25,'g'], ['Oil',200,'ml'], ['Wings Sauce',30,'ml'], ['Salt',2,'g'], ['Black Pepper',2,'g'],
            ]],
            ['menu_id'=>'MENU020', 'lines'=>[
                ['Chicken Breast',220,'g'], ['Flour',40,'g'], ['Egg',1,'pcs'], ['Breadcrumbs',40,'g'], ['Oil',350,'ml'], ['Spice Mix',10,'g'],
            ]],

            ['menu_id'=>'MENU021', 'lines'=>[
                ['Oreo Powder',25,'g'], ['Cream Base',30,'g'], ['Water',120,'ml'], ['Ice',200,'g'], ['Whipped Cream',25,'g'],
            ]],
            ['menu_id'=>'MENU022', 'lines'=>[
                ['Coffee Powder',20,'g'], ['Cream Base',30,'g'], ['Water',120,'ml'], ['Ice',200,'g'], ['Whipped Cream',25,'g'],
            ]],
            ['menu_id'=>'MENU023', 'lines'=>[
                ['Seasalt Caramel Syrup',25,'ml'], ['Cream Base',30,'g'], ['Water',120,'ml'], ['Ice',200,'g'], ['Whipped Cream',25,'g'],
            ]],

            ['menu_id'=>'MENU024', 'lines'=>[
                ['Ice',180,'g'], ['Fresh Milk',180,'ml'], ['Espresso',30,'ml'], ['Roasted Hazelnut Syrup',20,'ml'], ['Cinnamon',1,'g'],
            ]],
            ['menu_id'=>'MENU026', 'lines'=>[
                ['Ice',180,'g'], ['Fresh Milk',180,'ml'], ['Espresso',30,'ml'], ['Seasalt Caramel Syrup',20,'ml'], ['Caramel Sauce',10,'ml'],
            ]],

            ['menu_id'=>'MENU027', 'lines'=>[
                ['Fresh Milk',200,'ml'], ['Cocoa Powder',20,'g'], ['Condensed Milk',20,'ml'], ['Biscoff Milk',40,'ml'], ['Ice',150,'g'],
            ]],
            ['menu_id'=>'MENU028', 'lines'=>[
                ['Fresh Milk',200,'ml'], ['Biscoff Spread',25,'g'], ['Condensed Milk',20,'ml'], ['Ice',150,'g'],
            ]],
            ['menu_id'=>'MENU029', 'lines'=>[
                ['Fresh Milk',200,'ml'], ['Dark Chocolate Powder',15,'g'], ['Milk Chocolate Powder',15,'g'], ['Cocoa Powder',10,'g'],
                ['Condensed Milk',20,'ml'], ['Mallows',20,'g'], ['Ice',150,'g'],
            ]],

            ['menu_id'=>'MENU030', 'lines'=>[['Pizza Pack - 6 Cheese',1,'pcs']]],
            ['menu_id'=>'MENU031', 'lines'=>[['Pizza Pack - Triple Pepperoni',1,'pcs']]],
            ['menu_id'=>'MENU032', 'lines'=>[['Pizza Pack - Ultimate Hawaiian',1,'pcs']]],
            ['menu_id'=>'MENU033', 'lines'=>[['Mini Pizza Pack - 5 Cheese',1,'pcs']]],
            ['menu_id'=>'MENU034', 'lines'=>[['Mini Pizza Pack - Pepperoni',1,'pcs']]],
            ['menu_id'=>'MENU035', 'lines'=>[['Mini Pizza Pack - Creamy Spinach',1,'pcs']]],

            ['menu_id'=>'MENU036', 'lines'=>[['Fries Pack',200,'g']]],
            ['menu_id'=>'MENU037', 'lines'=>[['Chicken Poppers Pack',200,'g']]],
            ['menu_id'=>'MENU038', 'lines'=>[['Cheesestick Pack',5,'pcs']]],
        ];

        foreach ($recipes as $r) {
            foreach ($r['lines'] as $line) {
                [$ingredientName, $qty, $unit] = $line;

                if (!isset($ids[$ingredientName])) {
                    throw new \Exception("Ingredient not found: {$ingredientName}");
                }

                Recipe::updateOrCreate(
                 ['menu_id' => $r['menu_id'], 'ingredient_id' => $ids[$ingredientName]],
                ['qty_needed' => $qty]
                );

            }
        }
    }
}
