<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Hosting;
use App\Services\SuspendExpiredHostingService;

class SuspendExpiredHosting extends Command
{
    protected $signature = 'hosting:suspend-expired';
    protected $description = 'Suspend expired hosting subscriptions';
    
    public function handle()
    {
        $subscription=Hosting::where('status','active')->where('expiry_date','<=',now())->get();
        foreach($subscription as $sub){
            $this->suspendHosting($sub);
            $sub->update(['status'=>'suspended']);
            $this->info('ID: ' . $sub->id .' | Domain: ' . $sub->domain .' | Expiry: ' . $sub->expiry_date .' | Now: ' . now());   
    }
      
    }

    public function suspendHosting($hosting){

        }
};
