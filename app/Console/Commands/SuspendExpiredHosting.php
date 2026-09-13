<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Hosting;

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
          
    }
      
    $this->info('Expired hosting accounts suspended successfully.');
    }

    public function suspendHosting($hosting){

        }
};
