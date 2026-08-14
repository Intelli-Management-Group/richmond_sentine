<?php



namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ArticleDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;



class DeleteExpiredArticle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'article:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'It is used to delete expired articles';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {   
        
        try {
            $now = Carbon::now();
            
            // as of now only delete canadianPress articles only
            
            $articles = ArticleDetail::whereNotNull('expiryDate')
                ->where('expiryDate', '<=', $now)
                ->where('authorId', 8)
                ->get();
                
            $this->info('Found articles: ' . $articles->count());

            $deleted_articles = [];

            foreach ($articles as $article_data) {
                // Delete files, thumbnails, etc.
                if ((int)$article_data->isVideo) {
                    if ($article_data->videoFile && File::exists(public_path("uploads/video/{$article_data->videoFile}"))) {
                        File::delete(public_path("uploads/video/{$article_data->videoFile}"));
                    }
                    if ($article_data->videoThumbnail && File::exists(public_path("uploads/video_thumbnail/{$article_data->videoThumbnail}"))) {
                        File::delete(public_path("uploads/video_thumbnail/{$article_data->videoThumbnail}"));
                    }
                } else {
                    foreach ($article_data->articleFileDetail ?? [] as $f) {
                        if ($f->fileName && File::exists(public_path("uploads/article/{$f->fileName}"))) {
                            File::delete(public_path("uploads/article/{$f->fileName}"));
                        }
                        if ($f->thumbnailImage && File::exists(public_path("uploads/video_thumbnail/{$f->thumbnailImage}"))) {
                            File::delete(public_path("uploads/video_thumbnail/{$f->thumbnailImage}"));
                        }
                        $f->delete();
                    }
                }
                $article_data->delete();
                $deleted_articles[] = $article_data->id;

            }

            $fileName = 'delete_article_log.txt';
            File::append(public_path("uploads/{$fileName}"), json_encode($deleted_articles) . PHP_EOL);
          
            // $this->info('Deleted articles: ' . implode(', ', $deleted_articles));
            
            File::append(public_path("uploads/{$fileName}"), date("m/d/Y H:i:s") . PHP_EOL);
            return self::SUCCESS;
            
        } catch (\Throwable $e) {
            $fileName = 'delete_Article_log.txt';
            File::append(public_path("uploads/{$fileName}"), $e->getMessage() . PHP_EOL);
            
             File::append(public_path("uploads/{$fileName}"), date("m/d/Y H:i:s") . PHP_EOL);
            $this->error($e->getMessage());
            return self::FAILURE;
            
        }
    }
}