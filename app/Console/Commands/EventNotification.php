<?php

namespace App\Console\Commands;

use App\Models\CategoryDetail;
use Illuminate\Console\Command;

class EventNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'event:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'It is used to send upcoming event notification';

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
            $event_coupon_category_id = 0;
            $event_category_data = CategoryDetail::select("id")->where("name", "Events")->first();
            if ($event_category_data) {
                $event_coupon_category_id = $event_category_data->id;
            }
            sendNotification("Click to view upcoming events", "0", "Events", "event", $event_coupon_category_id);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
