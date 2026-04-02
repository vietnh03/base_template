<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CmsPage;
use App\Models\CmsSection;

class CmsSeeder extends Seeder
{
    public function run()
    {
        // Make seeder idempotent — safe to re-run
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        CmsSection::truncate();
        CmsPage::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $homePage = CmsPage::create([
            'name' => 'Home Page',
            'slug' => 'home',
            'is_active' => true,
        ]);

        $homePage->translations()->create([
            'locale' => 'vi',
            'meta_title' => 'International Toys - Tổng Kho Sỉ Đồ Chơi',
            'meta_keywords' => 'đồ chơi trẻ em, sỉ đồ chơi, toys, international toys',
            'meta_description' => 'Tổng kho sỉ đồ chơi trẻ em uy tín.',
        ]);

        $hero = CmsSection::create([
            'page_id' => $homePage->id,
            'type' => 'HeroSingle',
            'requires_data_source' => false,
            'sort_order' => 1,
        ]);

        $hero->translations()->create([
            'locale' => 'vi',
            'content' => [
                'title' => '',
                'image_url' => 'https://api.dochoiquocte.com/uploads/homeConfig/cocc82ngtyc491occ82cc80chocc9biquocc82cc81ctecc82cc81web281290x570px2928129_1763192706467.jpg',
            ],
        ]);

        $features = CmsSection::create([
            'page_id' => $homePage->id,
            'type' => 'FeatureList',
            'requires_data_source' => false,
            'sort_order' => 2,
        ]);

        $features->translations()->create([
            'locale' => 'vi',
            'content' => [
                'items' => [
                    [
                        'title' => 'Giới thiệu công ty Đồ chơi quốc tế',
                        'image_url' => 'https://api.dochoiquocte.com/uploads/homeConfig/acc89nhbannerclip_1747271707420.jpg',
                    ],
                    [
                        'title' => 'Sản phẩm nổi bật',
                        'image_url' => 'https://api.dochoiquocte.com/uploads/homeConfig/istockphoto-1426832520-612x612_1747272951238.jpg',
                    ],
                    [
                        'title' => 'Đồ chơi Hot trend',
                        'image_url' => 'https://api.dochoiquocte.com/uploads/homeConfig/7_1747272356295.jpg',
                    ],
                ],
            ],
        ]);

        $productList = CmsSection::create([
            'page_id' => $homePage->id,
            'type' => 'ProductList',
            'requires_data_source' => true,
            'sort_order' => 3,
        ]);

        $productList->translations()->create([
            'locale' => 'vi',
            'content' => [
                'title' => 'HÀNG MỚI VỀ',
                'limit' => 8,
                'sort' => 'newest',
            ],
        ]);
    }
}
