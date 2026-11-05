<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ArticleDetail;
use Carbon\Carbon;

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
    protected $description = 'It is used to delete expired article';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $articles = ArticleDetail::whereRaw("expiryDate IS NOT NULL")->get();
            $deleted_articles = array();
            foreach (count((array)$articles) > 0 ? $articles : array() as $article_data) {
                if (Carbon::now()->timestamp >= Carbon::parse($article_data->expiryDate)->timestamp) {
                    $is_video = intval($article_data->isVideo);
                    if ($is_video) {
                        if ($article_data->videoFile != null && file_exists(public_path("uploads/video/" . $article_data->videoFile))) {
                            unlink(public_path("uploads/video/" . $article_data->videoFile));
                        }
                        if ($article_data->videoThumbnail != null && file_exists(public_path("uploads/video_thumbnail/" . $article_data->videoThumbnail))) {
                            unlink(public_path("uploads/video_thumbnail/" . $article_data->videoThumbnail));
                        }
                    } else {
                        foreach (count((array)$article_data->articleFileDetail) > 0 ? $article_data->articleFileDetail : array() as $f) {
                            if ($f->fileName != null && file_exists(public_path("uploads/article/" . $f->fileName))) {
                                unlink(public_path("uploads/article/" . $f->fileName));
                            }
                            if ($f->thumbnailImage != null && file_exists(public_path("uploads/video_thumbnail/" . $f->thumbnailImage))) {
                                unlink(public_path("uploads/video_thumbnail/" . $f->thumbnailImage));
                            }
                            $f->delete();
                        }
                    }
                    $article_data->delete();
                    array_push($deleted_articles, $article_data->id);
                }
            }
             $fileName = 'datafile.txt';
	         \File::append(public_path('/uploads/'.$fileName),json_encode($deleted_articles));
        } catch (\Exception $e) {
             $fileName = 'datafile.txt';
	         \File::append(public_path('/uploads/'.$fileName),$e->getMessage());
            return $e->getMessage();
        }
    }
}
