<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\Product;
use App\V1\Models\ProductModel;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\SitemapIndex;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-sitemap';
    protected $urls = [];

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $data[] = [
            "url" => "https://gak.vn/vi",
            "updated_at" => Carbon::parse("2024-05-26 09:00"),
            "priority" => 0.8,
            "freq" => "daily",
            "changefreq" => "daily",
        ];
        $data[] = [
            "url" => "https://gak.vn/en",
            "updated_at" => Carbon::parse("2024-05-26 09:00"),
            "priority" => 0.8,
            "freq" => "daily",
            "changefreq" => "daily",
        ];
        $data[] = [
            "url" => "https://gak.vn/vi/tat-ca-san-pham",
            "updated_at" => Carbon::parse("2024-05-26 09:00"),
            "priority" => 0.8,
            "freq" => "daily",
            "changefreq" => "daily",
        ];
        $data[] = [
            "url" => "https://gak.vn/en/tat-ca-san-pham",
            "updated_at" => Carbon::parse("2024-05-26 09:00"),
            "priority" => 0.8,
            "freq" => "daily",
            "changefreq" => "daily",
        ];

        $this->generateSitemap($data, "/home/DEV-GAK-UI/public/vi/static-pages.xml", 0.8);
        $this->generateSitemap($data, "/home/DEV-GAK-UI/public/en/static-pages.xml", 0.8);
        $this->urls[] = "vi/static-pages.xml";
        $this->urls[] = "en/static-pages.xml";

        $this->product();
        $this->catrgory();
        $this->post();
        $this->page();

        $sitemap = SitemapIndex::create("https://gak.vn");
        foreach ($this->urls as $url) {
            $sitemap->add("https://gak.vn/$url");
        }
        $sitemap->writeToFile("/home/DEV-GAK-UI/public/sitemap.xml");

        Log::info('Generate the sitemap.');
    }


    public function product($priority = 0.6)
    {
        $productModel = new ProductModel();

        $input['is_active'] = 1;

        $result = $productModel->search($input,
            ['attributeVariants',
                'variants' => function ($query) {
                    $query->where('qty', '>', 0);
                }
            ]);
        $data = [];
        $dataEn = [];

        foreach ($result as $item) {
            $data[] = [
                "url" => "https://gak.vn/vi/product/$item->slug",
                //                "image"      => getImageCustom($item->image, '/images/building.png'),
                //                "title"      => "$item->title",
                "created_at" => Carbon::parse($item->created_at),
                "updated_at" => Carbon::parse($item->updated_at),

            ];
            $dataEn[] = [
                "url" => "https://gak.vn/en/product/$item->slug",
                //                "image"      => getImageCustom($item->image, '/images/building.png'),
                //                "title"      => "$item->title",
                "created_at" => Carbon::parse($item->created_at),
                "updated_at" => Carbon::parse($item->updated_at),

            ];
        }

        $data = array_chunk($data, 200);
        $dataEn = array_chunk($dataEn, 200);

        foreach ($data as $key => $item) {
            $this->generateSitemap($item, "/home/DEV-GAK-UI/public/vi/product$key.xml", $priority);
            $this->urls[] = "vi/product$key.xml";
        }

        foreach ($dataEn as $key => $item) {
            $this->generateSitemap($item, "/home/DEV-GAK-UI/public/en/product$key.xml", $priority);
            $this->urls[] = "en/product$key.xml";
        }
    }


    public function catrgory($priority = 0.8)
    {
        $result = Category::query()
            ->where('is_active', 1)
            ->get();
        $data = [];
        $dataEn = [];

        foreach ($result as $item) {
            $data[] = [
                "url" => "https://gak.vn/vi/collection/$item->slug",
                //                "image"      => getImageCustom($item->image, '/images/building.png'),
                //                "title"      => "$item->title",
                "created_at" => Carbon::parse($item->created_at),
                "updated_at" => Carbon::parse($item->updated_at),
                ];

            $dataEn[] = [
                "url" => "https://gak.vn/en/collection/$item->slug",
                //                "image"      => getImageCustom($item->image, '/images/building.png'),
                //                "title"      => "$item->title",
                "created_at" => Carbon::parse($item->created_at),
                "updated_at" => Carbon::parse($item->updated_at),

            ];
        }

        $data = array_chunk($data, 200);
        $dataEn = array_chunk($dataEn, 200);

        foreach ($data as $key => $item) {
            $this->generateSitemap($item, "/home/DEV-GAK-UI/public/vi/category$key.xml", $priority);
            $this->urls[] = "vi/category$key.xml";
        }

        foreach ($dataEn as $key => $item) {
            $this->generateSitemap($item, "/home/DEV-GAK-UI/public/en/category$key.xml", $priority);
            $this->urls[] = "en/category$key.xml";
        }
    }

    public function post($priority = 0.5)
    {
        $result = Post::query()
            ->where('is_active', 1)
            ->get();
        $data = [];
        $dataEn = [];

        foreach ($result as $item) {
            $data[] = [
                "url" => "https://gak.vn/vi/articles/$item->slug",
                //                "image"      => getImageCustom($item->image, '/images/building.png'),
                //                "title"      => "$item->title",
                "created_at" => Carbon::parse($item->created_at),
                "updated_at" => Carbon::parse($item->updated_at),

            ];
            $dataEn[] = [
                "url" => "https://gak.vn/en/articles/$item->slug",
                //                "image"      => getImageCustom($item->image, '/images/building.png'),
                //                "title"      => "$item->title",
                "created_at" => Carbon::parse($item->created_at),
                "updated_at" => Carbon::parse($item->updated_at),

            ];
        }

        $data = array_chunk($data, 200);
        $dataEn = array_chunk($dataEn, 200);

        foreach ($data as $key => $item) {
            $this->generateSitemap($item, "/home/DEV-GAK-UI/public/vi/article$key.xml", $priority);
            $this->urls[] = "vi/article$key.xml";
        }

        foreach ($dataEn as $key => $item) {
            $this->generateSitemap($item, "/home/DEV-GAK-UI/public/en/article$key.xml", $priority);
            $this->urls[] = "en/article$key.xml";
        }
    }

    public function page($priority = 0.8)
    {
        $result = Page::query()
            ->where('is_active', 1)
            ->where('is_button', 0)
            ->get();
        $data = [];
        $dataEn = [];

        foreach ($result as $item) {
            $data[] = [
                "url" => "https://gak.vn/vi/$item->slug",
                //                "image"      => getImageCustom($item->image, '/images/building.png'),
                //                "title"      => "$item->title",
                "created_at" => Carbon::parse($item->created_at),
                "updated_at" => Carbon::parse($item->updated_at),

            ];
            $dataEn[] = [
                "url" => "https://gak.vn/en/$item->slug",
                //                "image"      => getImageCustom($item->image, '/images/building.png'),
                //                "title"      => "$item->title",
                "created_at" => Carbon::parse($item->created_at),
                "updated_at" => Carbon::parse($item->updated_at),

            ];
        }

        $data = array_chunk($data, 200);
        $dataEn = array_chunk($dataEn, 200);

        foreach ($data as $key => $item) {
            $this->generateSitemap($item, "/home/DEV-GAK-UI/public/vi/page$key.xml", $priority);
            $this->urls[] = "vi/page$key.xml";
        }

        foreach ($dataEn as $key => $item) {
            $this->generateSitemap($item, "/home/DEV-GAK-UI/public/en/page$key.xml", $priority);
            $this->urls[] = "en/page$key.xml";
        }
    }

    public function generateSitemap($data, $path, $priority = 0.8, $changeFrequency = Url::CHANGE_FREQUENCY_DAILY)
    {
        $sitemap = Sitemap::create("https://gak.vn");

        foreach ($data as $item) {
            $sitemap->add(Url::create($item['url'])
                ->setLastModificationDate($item['updated_at'])
                ->setChangeFrequency($changeFrequency)
                ->setPriority($priority));
        }
        $sitemap->writeToFile($path);
    }
}
