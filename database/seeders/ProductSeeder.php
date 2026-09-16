<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds for Cafe Berco individual products.
     */
    public function run(): void
    {
        // 1. ENSURE 5 MAIN CATEGORIES EXIST
        $categoriesData = [
            'Classic Coffee' => ['slug' => 'classic-coffee', 'icon' => 'fas fa-coffee'],
            'Non Coffee' => ['slug' => 'non-coffee', 'icon' => 'fas fa-mug-hot'],
            'Food' => ['slug' => 'food', 'icon' => 'fas fa-utensils'],
            'Snack' => ['slug' => 'snack', 'icon' => 'fas fa-cookie-bite'],
            'Dessert' => ['slug' => 'dessert', 'icon' => 'fas fa-cake-candles'],
        ];

        $categoryMap = [];
        foreach ($categoriesData as $name => $meta) {
            $cat = Category::firstOrCreate(['nama_kategori' => $name], $meta);
            $categoryMap[$name] = $cat->id;
        }

        // 2. COMPLETE LIST OF 55 INDIVIDUAL PRODUCTS
        $products = [
            // ==========================================
            // 1. CLASSIC COFFEE
            // ==========================================
            // Series: Black
            [
                'category' => 'Classic Coffee',
                'series' => 'Black',
                'name' => 'Americano',
                'deskripsi' => 'Double shot espresso murni dipadukan dengan air mineral menghasilkan cita rasa clean & bold.',
                'has_temperature_option' => true,
                'temperature_options' => [
                    ['type' => 'Hot', 'price' => 20000, 'hpp' => 6500],
                    ['type' => 'Cold', 'price' => 20000, 'hpp' => 7500],
                ],
            ],
            [
                'category' => 'Classic Coffee',
                'series' => 'Black',
                'name' => 'Long Black',
                'deskripsi' => 'Espresso di atas air panas menjaga crema tebal dengan aroma biji kopi yang intens.',
                'has_temperature_option' => true,
                'temperature_options' => [
                    ['type' => 'Hot', 'price' => 20000, 'hpp' => 6500],
                    ['type' => 'Cold', 'price' => 20000, 'hpp' => 7500],
                ],
            ],

            // Series: White
            [
                'category' => 'Classic Coffee',
                'series' => 'White',
                'name' => 'Cappuccino',
                'deskripsi' => 'Espresso bold dengan foam susu tebal dan silky, taburan bubuk cokelat di atasnya.',
                'has_temperature_option' => true,
                'temperature_options' => [
                    ['type' => 'Hot', 'price' => 22000, 'hpp' => 8500],
                    ['type' => 'Cold', 'price' => 18000, 'hpp' => 8000],
                ],
            ],
            [
                'category' => 'Classic Coffee',
                'series' => 'White',
                'name' => 'Caffe Latte',
                'deskripsi' => 'Perpaduan lembut espresso dengan fresh milk steamed berbusa mikro yang creamy.',
                'has_temperature_option' => true,
                'temperature_options' => [
                    ['type' => 'Hot', 'price' => 22000, 'hpp' => 8500],
                    ['type' => 'Cold', 'price' => 18000, 'hpp' => 8000],
                ],
            ],
            [
                'category' => 'Classic Coffee',
                'series' => 'White',
                'name' => 'Magic',
                'deskripsi' => 'Ristretto ganda dengan steamed milk porsi seimbang, rasa kopi lebih dominan dan manis natural.',
                'has_temperature_option' => true,
                'temperature_options' => [
                    ['type' => 'Hot', 'price' => 22000, 'hpp' => 8500],
                    ['type' => 'Cold', 'price' => 18000, 'hpp' => 8000],
                ],
            ],

            // Series: Kopi Susu Flavored
            [
                'category' => 'Classic Coffee',
                'series' => 'Kopi Susu Flavored',
                'name' => 'Kopi Susu Hazelnut',
                'deskripsi' => 'Kopi susu khas Berco dengan aroma kacang hazelnut panggang yang gurih dan harum.',
                'has_temperature_option' => true,
                'temperature_options' => [
                    ['type' => 'Hot', 'price' => 22000, 'hpp' => 9500],
                    ['type' => 'Cold', 'price' => 18000, 'hpp' => 9000],
                ],
            ],
            [
                'category' => 'Classic Coffee',
                'series' => 'Kopi Susu Flavored',
                'name' => 'Kopi Susu Caramel',
                'deskripsi' => 'Kopi susu berbalut sirup karamel emas dengan rasa manis legit yang memanjakan lidah.',
                'has_temperature_option' => true,
                'temperature_options' => [
                    ['type' => 'Hot', 'price' => 22000, 'hpp' => 9500],
                    ['type' => 'Cold', 'price' => 18000, 'hpp' => 9000],
                ],
            ],
            [
                'category' => 'Classic Coffee',
                'series' => 'Kopi Susu Flavored',
                'name' => 'Kopi Susu Vanilla',
                'deskripsi' => 'Kopi susu lembut dengan sentuhan vanila Madagaskar yang manis dan menenangkan.',
                'has_temperature_option' => true,
                'temperature_options' => [
                    ['type' => 'Hot', 'price' => 22000, 'hpp' => 9500],
                    ['type' => 'Cold', 'price' => 18000, 'hpp' => 9000],
                ],
            ],
            [
                'category' => 'Classic Coffee',
                'series' => 'Kopi Susu Flavored',
                'name' => 'Kopi Susu Butterscotch',
                'deskripsi' => 'Sensasi unik butterscotch manis mentega gurih berpadu sempurna dengan kopi espresso.',
                'has_temperature_option' => true,
                'temperature_options' => [
                    ['type' => 'Hot', 'price' => 22000, 'hpp' => 9500],
                    ['type' => 'Cold', 'price' => 18000, 'hpp' => 9000],
                ],
            ],
            [
                'category' => 'Classic Coffee',
                'series' => 'Kopi Susu Flavored',
                'name' => 'Kopi Susu Gula Aren',
                'deskripsi' => 'Kopi susu gula aren organik Banyuwangi asli, smokey, legit, dan nagih.',
                'has_temperature_option' => true,
                'temperature_options' => [
                    ['type' => 'Hot', 'price' => 22000, 'hpp' => 9500],
                    ['type' => 'Cold', 'price' => 18000, 'hpp' => 9000],
                ],
            ],
            [
                'category' => 'Classic Coffee',
                'series' => 'Kopi Susu Flavored',
                'name' => 'Kopi Susu Moccacino',
                'deskripsi' => 'Perpaduan klasik antara espresso kental, fresh milk, dan cokelat murni pekat.',
                'has_temperature_option' => true,
                'temperature_options' => [
                    ['type' => 'Hot', 'price' => 22000, 'hpp' => 9500],
                    ['type' => 'Cold', 'price' => 18000, 'hpp' => 9000],
                ],
            ],

            // Series: Signature
            [
                'category' => 'Classic Coffee',
                'series' => 'Signature',
                'name' => 'Kopi Susu Berco',
                'deskripsi' => 'Resep racikan susu legendaris khas Berco dengan espresso bold, rasa creamy sempurna.',
                'has_temperature_option' => true,
                'temperature_options' => [
                    ['type' => 'Hot', 'price' => 18000, 'hpp' => 7500],
                    ['type' => 'Cold', 'price' => 18000, 'hpp' => 7500],
                ],
            ],
            [
                'category' => 'Classic Coffee',
                'series' => 'Signature',
                'name' => 'Lemonade Americano',
                'deskripsi' => 'Sensasi dingin sari lemon segar, air soda berkarbonasi, dan espresso aromatik.',
                'has_temperature_option' => false,
                'temperature_options' => [
                    ['type' => 'Cold', 'price' => 18000, 'hpp' => 8000],
                ],
            ],

            // ==========================================
            // 2. NON COFFEE
            // ==========================================
            // Series: Summer
            [
                'category' => 'Non Coffee',
                'series' => 'Summer',
                'name' => 'Summer Strawberry',
                'deskripsi' => 'Minuman buah stroberi segar dingin pemantik semangat di hari cerah.',
                'has_temperature_option' => false,
                'temperature_options' => [
                    ['type' => 'Cold', 'price' => 17000, 'hpp' => 6000],
                ],
            ],
            [
                'category' => 'Non Coffee',
                'series' => 'Summer',
                'name' => 'Summer Lychee',
                'deskripsi' => 'Sajian jus leci manis aromatik dengan es batu kristal menyegarkan.',
                'has_temperature_option' => false,
                'temperature_options' => [
                    ['type' => 'Cold', 'price' => 17000, 'hpp' => 6000],
                ],
            ],
            [
                'category' => 'Non Coffee',
                'series' => 'Summer',
                'name' => 'Summer Orange',
                'deskripsi' => 'Konsentrat jeruk sitrus segar pelepas dahaga seketika.',
                'has_temperature_option' => false,
                'temperature_options' => [
                    ['type' => 'Cold', 'price' => 17000, 'hpp' => 6000],
                ],
            ],

            // Series: Sparkling Drink
            [
                'category' => 'Non Coffee',
                'series' => 'Sparkling Drink',
                'name' => 'Sparkling Strawberry',
                'deskripsi' => 'Mocktail soda stroberi berkarbonasi dingin dengan sensasi letupan asam manis.',
                'has_temperature_option' => false,
                'temperature_options' => [
                    ['type' => 'Cold', 'price' => 17000, 'hpp' => 6500],
                ],
            ],
            [
                'category' => 'Non Coffee',
                'series' => 'Sparkling Drink',
                'name' => 'Sparkling Lychee',
                'deskripsi' => 'Soda leci bergelembung segar dengan rasa manis buah tropis alami.',
                'has_temperature_option' => false,
                'temperature_options' => [
                    ['type' => 'Cold', 'price' => 17000, 'hpp' => 6500],
                ],
            ],
            [
                'category' => 'Non Coffee',
                'series' => 'Sparkling Drink',
                'name' => 'Sparkling Orange',
                'deskripsi' => 'Soda jeruk bersoda dingin dengan rasa segar meledak di tenggorokan.',
                'has_temperature_option' => false,
                'temperature_options' => [
                    ['type' => 'Cold', 'price' => 17000, 'hpp' => 6500],
                ],
            ],

            // Series: Tea
            [
                'category' => 'Non Coffee',
                'series' => 'Tea',
                'name' => 'Lychee Tea',
                'deskripsi' => 'Seduhan teh hitam pilihan berpadu dengan manis buah leci aromatik.',
                'has_temperature_option' => true,
                'temperature_options' => [
                    ['type' => 'Hot', 'price' => 15000, 'hpp' => 5000],
                    ['type' => 'Cold', 'price' => 15000, 'hpp' => 5000],
                ],
            ],
            [
                'category' => 'Non Coffee',
                'series' => 'Tea',
                'name' => 'Lemon Tea',
                'deskripsi' => 'Teh segar dipadukan perasan sari lemon asli yang menyegarkan tubuh.',
                'has_temperature_option' => true,
                'temperature_options' => [
                    ['type' => 'Hot', 'price' => 15000, 'hpp' => 5000],
                    ['type' => 'Cold', 'price' => 15000, 'hpp' => 5000],
                ],
            ],
            [
                'category' => 'Non Coffee',
                'series' => 'Tea',
                'name' => 'Berry Tea',
                'deskripsi' => 'Seduhan black tea wangi berpadu sari buah berry asam manis menyegarkan.',
                'has_temperature_option' => true,
                'temperature_options' => [
                    ['type' => 'Hot', 'price' => 15000, 'hpp' => 5000],
                    ['type' => 'Cold', 'price' => 15000, 'hpp' => 5000],
                ],
            ],

            // Series: Milkbase
            [
                'category' => 'Non Coffee',
                'series' => 'Milkbase',
                'name' => 'Chocolate Milk',
                'deskripsi' => 'Susu segar berpadu bubuk kakao murni pekat yang kaya rasa dan gurih.',
                'has_temperature_option' => true,
                'temperature_options' => [
                    ['type' => 'Hot', 'price' => 20000, 'hpp' => 7500],
                    ['type' => 'Cold', 'price' => 15000, 'hpp' => 7000],
                ],
            ],
            [
                'category' => 'Non Coffee',
                'series' => 'Milkbase',
                'name' => 'Taro Milk',
                'deskripsi' => 'Minuman susu rasa talas taro wangi bertekstur lembut dan manis pas.',
                'has_temperature_option' => true,
                'temperature_options' => [
                    ['type' => 'Hot', 'price' => 20000, 'hpp' => 7500],
                    ['type' => 'Cold', 'price' => 15000, 'hpp' => 7000],
                ],
            ],
            [
                'category' => 'Non Coffee',
                'series' => 'Milkbase',
                'name' => 'Green Tea Milk',
                'deskripsi' => 'Matcha hijau harum dipadukan dengan fresh milk lembut.',
                'has_temperature_option' => true,
                'temperature_options' => [
                    ['type' => 'Hot', 'price' => 20000, 'hpp' => 7500],
                    ['type' => 'Cold', 'price' => 15000, 'hpp' => 7000],
                ],
            ],

            // Series: Matcha
            [
                'category' => 'Non Coffee',
                'series' => 'Matcha',
                'name' => 'Matcha Latte',
                'deskripsi' => 'Pure Uji Matcha autentik berpadu fresh milk lembut dan aroma earthy.',
                'has_temperature_option' => true,
                'temperature_options' => [
                    ['type' => 'Hot', 'price' => 20000, 'hpp' => 8000],
                    ['type' => 'Cold', 'price' => 20000, 'hpp' => 8000],
                ],
            ],
            [
                'category' => 'Non Coffee',
                'series' => 'Matcha',
                'name' => 'Matcha Strawberry',
                'deskripsi' => 'Layering cantik pure matcha dengan puree stroberi manis asam dan susu.',
                'has_temperature_option' => true,
                'temperature_options' => [
                    ['type' => 'Hot', 'price' => 20000, 'hpp' => 8000],
                    ['type' => 'Cold', 'price' => 20000, 'hpp' => 8000],
                ],
            ],
            [
                'category' => 'Non Coffee',
                'series' => 'Matcha',
                'name' => 'Matcha Orange',
                'deskripsi' => 'Kombinasi unik matcha tebal dan sitrus jeruk segar yang menyatu indah.',
                'has_temperature_option' => true,
                'temperature_options' => [
                    ['type' => 'Hot', 'price' => 20000, 'hpp' => 8000],
                    ['type' => 'Cold', 'price' => 20000, 'hpp' => 8000],
                ],
            ],
            [
                'category' => 'Non Coffee',
                'series' => 'Matcha',
                'name' => 'Matchacano',
                'deskripsi' => 'Pure matcha diseduh americano style dengan air dingin, rasa teh hijau murni.',
                'has_temperature_option' => true,
                'temperature_options' => [
                    ['type' => 'Hot', 'price' => 20000, 'hpp' => 8000],
                    ['type' => 'Cold', 'price' => 20000, 'hpp' => 8000],
                ],
            ],

            // Series: Lokal Pride
            [
                'category' => 'Non Coffee',
                'series' => 'Lokal Pride',
                'name' => 'Jahe Hangat',
                'deskripsi' => 'Seduhan rempah jahe emprit asli dengan gula merah hangat di tubuh.',
                'has_temperature_option' => false,
                'temperature_options' => [
                    ['type' => 'Hot', 'price' => 10000, 'hpp' => 4000],
                ],
            ],
            [
                'category' => 'Non Coffee',
                'series' => 'Lokal Pride',
                'name' => 'Susu Jahe',
                'deskripsi' => 'Perpaduan jahe seduh hangat dengan manis kental susu gurih bernutrisi.',
                'has_temperature_option' => true,
                'temperature_options' => [
                    ['type' => 'Hot', 'price' => 10000, 'hpp' => 4000],
                    ['type' => 'Cold', 'price' => 12000, 'hpp' => 5000],
                ],
            ],
            [
                'category' => 'Non Coffee',
                'series' => 'Lokal Pride',
                'name' => 'Jhosua',
                'deskripsi' => 'Minuman legendaris ekstra joss dipadu susu kental manis dingin menyegarkan.',
                'has_temperature_option' => false,
                'temperature_options' => [
                    ['type' => 'Cold', 'price' => 12000, 'hpp' => 5000],
                ],
            ],
            [
                'category' => 'Non Coffee',
                'series' => 'Lokal Pride',
                'name' => 'Sogem',
                'deskripsi' => 'Soda gembira sirup mawar, kental manis, dan air soda segar dingin.',
                'has_temperature_option' => false,
                'temperature_options' => [
                    ['type' => 'Cold', 'price' => 12000, 'hpp' => 5000],
                ],
            ],

            // ==========================================
            // 3. FOOD
            // ==========================================
            [
                'category' => 'Food',
                'series' => 'Main Course',
                'name' => 'Nasi Goreng Jawa',
                'deskripsi' => 'Nasi goreng bumbu rempah tradisional Jawa dengan telur mata sapi dan acar.',
                'has_temperature_option' => false,
                'price' => 20000,
                'hpp' => 8500,
            ],
            [
                'category' => 'Food',
                'series' => 'Main Course',
                'name' => 'Nasi Goreng Seafood',
                'deskripsi' => 'Nasi goreng gurih dengan topping udang, cumi segar, dan telur orak-arik.',
                'has_temperature_option' => false,
                'price' => 28000,
                'hpp' => 12000,
            ],
            [
                'category' => 'Food',
                'series' => 'Main Course',
                'name' => 'Mie Nyemek',
                'deskripsi' => 'Mie kuah kental gurih khas nusantara dimasak telur orak-arik dan sayur segar.',
                'has_temperature_option' => false,
                'price' => 15000,
                'hpp' => 6500,
            ],
            [
                'category' => 'Food',
                'series' => 'Main Course',
                'name' => 'Mie Goreng',
                'deskripsi' => 'Mie telur goreng bumbu spesial dengan sayuran hijau dan taburan bawang goreng.',
                'has_temperature_option' => false,
                'price' => 15000,
                'hpp' => 6500,
            ],
            [
                'category' => 'Food',
                'series' => 'Main Course',
                'name' => 'Kwetiau',
                'deskripsi' => 'Kwetiau beras kenyal dimasak gurih dengan sayuran segar dan telur.',
                'has_temperature_option' => false,
                'price' => 20000,
                'hpp' => 8000,
            ],
            [
                'category' => 'Food',
                'series' => 'Main Course',
                'name' => 'Chicken Blackpaper',
                'deskripsi' => 'Daging ayam fillet saus lada hitam pedas gurih disajikan bersama nasi putih hangat.',
                'has_temperature_option' => false,
                'price' => 22000,
                'hpp' => 9500,
            ],
            [
                'category' => 'Food',
                'series' => 'Main Course',
                'name' => 'Ayam Chili Padi',
                'deskripsi' => 'Ayam goreng renyah bumbu chili padi pedas gurih disajikan bersama nasi putih.',
                'has_temperature_option' => false,
                'price' => 20000,
                'hpp' => 9000,
            ],

            // ==========================================
            // 4. SNACK
            // ==========================================
            [
                'category' => 'Snack',
                'series' => 'Finger Food & Platter',
                'name' => 'French Fries',
                'deskripsi' => 'Kentang goreng shoestring renyah bertabur bumbu gurih khas Berco.',
                'has_temperature_option' => false,
                'price' => 12000,
                'hpp' => 5000,
            ],
            [
                'category' => 'Snack',
                'series' => 'Finger Food & Platter',
                'name' => 'Nugget Ayam',
                'deskripsi' => 'Nugget ayam goreng renyah dengan saus sambal dan mayones.',
                'has_temperature_option' => false,
                'price' => 12000,
                'hpp' => 5000,
            ],
            [
                'category' => 'Snack',
                'series' => 'Finger Food & Platter',
                'name' => 'Mix Snack',
                'deskripsi' => 'Platter kombinasi kentang goreng, 3 pcs nugget ayam, dan 2 pcs sosis sapi.',
                'has_temperature_option' => false,
                'price' => 20000,
                'hpp' => 8500,
            ],
            [
                'category' => 'Snack',
                'series' => 'Finger Food & Platter',
                'name' => 'Risol Original',
                'deskripsi' => 'Risol gurih renyah isi 3 pcs dengan saus sambal cocolan nikmat.',
                'has_temperature_option' => false,
                'price' => 15000,
                'hpp' => 6500,
            ],
            [
                'category' => 'Snack',
                'series' => 'Finger Food & Platter',
                'name' => 'Risol Mayones',
                'deskripsi' => 'Risol renyah isi telur, daging asap, dan saus creamy mayones meleleh.',
                'has_temperature_option' => false,
                'price' => 15000,
                'hpp' => 6500,
            ],
            [
                'category' => 'Snack',
                'series' => 'Finger Food & Platter',
                'name' => 'Lumpia Sayur',
                'deskripsi' => 'Lumpia goreng kulit renyah isi rebung dan sayuran gurih.',
                'has_temperature_option' => false,
                'price' => 15000,
                'hpp' => 6000,
            ],
            [
                'category' => 'Snack',
                'series' => 'Finger Food & Platter',
                'name' => 'Cireng Salju + Saus Bangkok',
                'deskripsi' => 'Cireng salju kenyal renyah 10 pcs dengan cocolan saus bangkok asam manis pedas.',
                'has_temperature_option' => false,
                'price' => 12000,
                'hpp' => 4500,
            ],
            [
                'category' => 'Snack',
                'series' => 'Finger Food & Platter',
                'name' => 'Tahu Walik',
                'deskripsi' => 'Tahu walik renyah khas Banyuwangi dengan isian adonan ayam gurih.',
                'has_temperature_option' => false,
                'price' => 12000,
                'hpp' => 5000,
            ],
            [
                'category' => 'Snack',
                'series' => 'Finger Food & Platter',
                'name' => 'Tahu Petis',
                'deskripsi' => 'Tahu goreng hangat disajikan dengan saus petis udang gurih khas Jawa Timur.',
                'has_temperature_option' => false,
                'price' => 10000,
                'hpp' => 4000,
            ],
            [
                'category' => 'Snack',
                'series' => 'Finger Food & Platter',
                'name' => 'Sosis Bakar',
                'deskripsi' => 'Sosis sapi jumbo panggang diolesi saus barbekyu manis gurih.',
                'has_temperature_option' => false,
                'price' => 20000,
                'hpp' => 8000,
            ],

            // ==========================================
            // 5. DESSERT
            // ==========================================
            [
                'category' => 'Dessert',
                'series' => 'Sweet & Pastry',
                'name' => 'Donut Berco Original',
                'deskripsi' => 'Donat kentang empuk isi 2 pcs dengan taburan gula halus salju klasik.',
                'has_temperature_option' => false,
                'price' => 15000,
                'hpp' => 5500,
            ],
            [
                'category' => 'Dessert',
                'series' => 'Sweet & Pastry',
                'name' => 'Donut Berco Chocolate',
                'deskripsi' => 'Donat kentang empuk isi 2 pcs dilapisi glaze cokelat Belgia lumer.',
                'has_temperature_option' => false,
                'price' => 15000,
                'hpp' => 5500,
            ],
            [
                'category' => 'Dessert',
                'series' => 'Sweet & Pastry',
                'name' => 'Donut Berco Matcha',
                'deskripsi' => 'Donat kentang isi 2 pcs dengan siraman glaze teh hijau matcha harum manis.',
                'has_temperature_option' => false,
                'price' => 15000,
                'hpp' => 5500,
            ],
            [
                'category' => 'Dessert',
                'series' => 'Sweet & Pastry',
                'name' => 'Donut Berco Tiramisu',
                'deskripsi' => 'Donat kentang isi 2 pcs berbalut glaze tiramisu kopi vanila lezat.',
                'has_temperature_option' => false,
                'price' => 15000,
                'hpp' => 5500,
            ],
            [
                'category' => 'Dessert',
                'series' => 'Sweet & Pastry',
                'name' => 'Churros Original',
                'deskripsi' => 'Churros renyah khas Spanyol dengan taburan cinnamon sugar dan saus cokelat.',
                'has_temperature_option' => false,
                'price' => 17000,
                'hpp' => 6000,
            ],
            [
                'category' => 'Dessert',
                'series' => 'Sweet & Pastry',
                'name' => 'Pisang Chocolate Original',
                'deskripsi' => 'Pisang goreng karamel krispi berbalut cokelat leleh dan keju parut.',
                'has_temperature_option' => false,
                'price' => 15000,
                'hpp' => 5500,
            ],
        ];

        // 3. SEED INDIVIDUAL PRODUCTS INTO MENUS TABLE
        foreach ($products as $item) {
            $catId = $categoryMap[$item['category']] ?? null;

            $defaultPrice = $item['price'] ?? 0;
            $defaultHpp = $item['hpp'] ?? 0;

            if (! empty($item['temperature_options'])) {
                $firstOpt = $item['temperature_options'][0];
                foreach ($item['temperature_options'] as $t) {
                    if (strcasecmp($t['type'], 'Hot') === 0) {
                        $firstOpt = $t;
                        break;
                    }
                }
                $defaultPrice = $firstOpt['price'] ?? $defaultPrice;
                $defaultHpp = $firstOpt['hpp'] ?? $defaultHpp;
            }

            Menu::updateOrCreate(
                ['nama_menu' => $item['name']],
                [
                    'id_kategori' => $catId,
                    'series' => $item['series'] ?? null,
                    'harga' => $defaultPrice,
                    'hpp' => $defaultHpp,
                    'stok' => 50,
                    'status_tersedia' => true,
                    'deskripsi' => $item['deskripsi'] ?? '',
                    'has_temperature_option' => $item['has_temperature_option'] ?? false,
                    'temperature_options' => $item['temperature_options'] ?? null,
                    'sub_variants' => null,
                    'flavor_options' => null,
                    'variant_selection_rules' => null,
                    'portion_count' => 1,
                ]
            );
        }
    }
}
