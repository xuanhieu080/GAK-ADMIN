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
            "url"        => "https://gak.vn/vi",
            "updated_at" => Carbon::parse("2024-05-20 09:00"),
            "priority"   => 0.8,
            "freq"       => "daily",
            "changefreq" => "daily",
        ];

        $this->generateSitemap($data, "/home/Hegka-UI/public/static-pages.xml", 0.8);
        $this->urls[] = "static-pages.xml";
        
        $this->product();
        $this->catrgory();
        $this->post();
        $this->page();

        $sitemap = SitemapIndex::create("https://gak.vn");
        foreach ($this->urls as $url) {
            $sitemap->add("https://gak.vn/vi/$url");
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

        foreach ($result as $item) {
            $data[] = [
                "url"        => "https://gak.vn/vi/product/$item->slug",
                //                "image"      => getImageCustom($item->image, '/images/building.png'),
                //                "title"      => "$item->title",
                "created_at" => Carbon::parse($item->created_at),
                "updated_at" => Carbon::parse($item->updated_at),

            ];
        }

        $data = array_chunk($data, 200);

        foreach ($data as $key => $item) {
            $this->generateSitemap($item, "/home/DEV-GAK-UI/public/product$key.xml", $priority);
            $this->urls[] = "product$key.xml";
        }
    }


    public function catrgory($priority = 0.8)
    {
        $result = Category::query()
            ->where('is_active', 1)
            ->get();
        $data = [];

        foreach ($result as $item) {
            $data[] = [
                "url"        => "https://gak.vn/vi/collection/$item->slug",
                //                "image"      => getImageCustom($item->image, '/images/building.png'),
                //                "title"      => "$item->title",
                "created_at" => Carbon::parse($item->created_at),
                "updated_at" => Carbon::parse($item->updated_at),

            ];
        }

        $data = array_chunk($data, 200);

        foreach ($data as $key => $item) {
            $this->generateSitemap($item, "/home/DEV-GAK-UI/public/category$key.xml", $priority);
            $this->urls[] = "category$key.xml";
        }
    }

    public function post($priority = 0.5)
    {
        $result = Post::query()
            ->where('is_active', 1)
            ->get();
        $data = [];

        foreach ($result as $item) {
            $data[] = [
                "url"        => "https://gak.vn/vi/articles/$item->slug",
                //                "image"      => getImageCustom($item->image, '/images/building.png'),
                //                "title"      => "$item->title",
                "created_at" => Carbon::parse($item->created_at),
                "updated_at" => Carbon::parse($item->updated_at),

            ];
        }

        $data = array_chunk($data, 200);

        foreach ($data as $key => $item) {
            $this->generateSitemap($item, "/home/DEV-GAK-UI/public/article$key.xml", $priority);
            $this->urls[] = "article$key.xml";
        }
    }

    public function page($priority = 0.8)
    {
        $result = Page::query()
            ->where('is_active', 1)
            ->get();
        $data = [];

        foreach ($result as $item) {
            $data[] = [
                "url"        => "https://gak.vn/vi/$item->slug",
                //                "image"      => getImageCustom($item->image, '/images/building.png'),
                //                "title"      => "$item->title",
                "created_at" => Carbon::parse($item->created_at),
                "updated_at" => Carbon::parse($item->updated_at),

            ];
        }

        $data = array_chunk($data, 200);

        foreach ($data as $key => $item) {
            $this->generateSitemap($item, "/home/DEV-GAK-UI/public/page$key.xml", $priority);
            $this->urls[] = "page$key.xml";
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
