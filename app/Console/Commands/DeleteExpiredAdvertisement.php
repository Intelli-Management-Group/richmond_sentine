<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AdvertisementDetail;
use Carbon\Carbon;

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
            $advertisement = AdvertisementDetail::whereRaw("expiryDate IS NOT NULL")->get();
            $deleted_advertisement = array();
            foreach (count((array)$advertisement) > 0 ? $advertisement : array() as $data) {
                if (Carbon::now()->timestamp >= Carbon::parse($data->expiryDate)->timestamp) {
                    $data->status = 0;
                    $data->save();
                    array_push($deleted_advertisement,$data->id);
                }
            }
            $fileName = 'advertisement.txt';
	         \File::append(public_path('/uploads/'.$fileName),json_encode($deleted_advertisement));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
