<?php

namespace App\Console\Commands;

use App\Models\Article;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchArticlesFromSpaceflightNews extends Command
{
    const BATCH_SIZE = 1000;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:FetchArticlesFromSpaceflightNews';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch articles from space flight news API, storing articles, launches, and events in the database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $max_external_id = Article::max('external_id') ?? 0;

        $imported_amount = 0;
        do{
            $response = Http::get("https://api.spaceflightnewsapi.net/v3/articles?_sort=id&id_gt=$max_external_id&_limit=" . self::BATCH_SIZE);
            $articles = $response->json();

            foreach($articles as $article){
                Article::createFromExternalArticle($article);
                $imported_amount++;

                if($imported_amount % 100 == 0){
                    $this->info($imported_amount);
                }
            }

            if(end($articles)){
                $max_external_id = end($articles)["id"];
            }
        } while(count($articles) == self::BATCH_SIZE);

        $this->info("Import finished!");

        return Command::SUCCESS;
    }
}
