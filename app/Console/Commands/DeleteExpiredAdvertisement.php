<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AdvertisementDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class DeleteExpiredAdvertisement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'unpublish:advertisement';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Unpublish expired advertisement';

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
            $advertisement = AdvertisementDetail::whereRaw("expiryDate IS NOT NULL AND status IS NOT 0")->get();
            $deleted_advertisement = array();
            foreach (count((array)$advertisement) > 0 ? $advertisement : array() as $data) {
                if (Carbon::now()->timestamp >= Carbon::parse($data->expiryDate)->timestamp) {
                    $data->status = 0;
                    $data->save();
                    array_push($deleted_advertisement,$data->id);
                }
            }
            $fileName = 'unpublish_advertisement_log.txt';
            
	       //  File::append(public_path('/uploads/'.$fileName),json_encode($deleted_advertisement));
	         
	         File::append(public_path("uploads/{$fileName}"), date("m/d/Y H:i:s") . PHP_EOL);
        } catch (\Exception $e) {
            
            $fileName = 'unpublis_advertisement_log.txt';
            
            File::append(public_path("uploads/{$fileName}"), $e->getMessage() . PHP_EOL);
            File::append(public_path("uploads/{$fileName}"), date("m/d/Y H:i:s") . PHP_EOL);
            
            $this->error($e->getMessage());
            return 1;
        }
    }
}
