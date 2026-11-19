<?php

namespace App\Console\Commands;

use App\Support\Seo\SeoFiles;
use App\Support\Seo\SitemapBuilder;
use Illuminate\Console\Command;

class GenerateSitemap extends Command
{
    protected $signature = 'seo:sitemap:generate {--path= : Full output path; defaults to public/sitemap.xml}';

    protected $description = 'Generate the public XML sitemap from indexable content';

    public function handle(SitemapBuilder $sitemap): int
    {
        $path = $this->option('path') ?: SeoFiles::sitemapPath();

        $sitemap->build()->writeToFile($path);

        $this->components->info('Sitemap generated: '.$path);

        return self::SUCCESS;
    }
}
