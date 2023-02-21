<?php

namespace App\Console\Commands;

use App\Mail\ExceptionOccured;
use App\Models\Article;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Throwable;

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
        try{
            return $this->fetchArticlesFromSpaceflightNews();
        } catch(Throwable $throwable){
            $this->handleThrowable($throwable);
        }
    }

    private function fetchArticlesFromSpaceflightNews(){
        $max_external_id = Article::max('externalId') ?? 0;

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

    private function handleThrowable($throwable){
        $content['message'] = $throwable->getMessage();
        $content['file'] = $throwable->getFile();
        $content['line'] = $throwable->getLine();
        $content['trace'] = $throwable->getTrace();
        $content['url'] = request()->url();
        $content['body'] = request()->all();
        $content['ip'] = request()->ip();

        Mail::to(env('ALERT_MAIL_TO_ADDRESS'))->send(new ExceptionOccured($content));
    }
}
