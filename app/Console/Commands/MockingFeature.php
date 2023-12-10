<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RequestFeature;
use Illuminate\Support\Facades\Process;
use Illuminate\Process\Pool;

class MockingFeature extends Command
{
    // php artisan mocking:feature --process=10
    // 10 => 100.000 data 
    // 100 => 1.000.000 data
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mocking:feature {--process=0}';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $process = (int) $this->option("process");

        if($process){
            return $this->spawn($process);
        }

        for($i = 0;$i < 10000; $i++){
            if($i % 1000 === 0){
                echo ".";
            }  

            try {
                $this->insert();
            }catch(\Exception $e){
                \Log::info($e->getMessage());
            }
        }
    }

    public function spawn($process){
        Process::pool(function(Pool $pool) use ($process){
            for($i = 0;$i < $process; $i++){
                $pool->command("php artisan mocking:feature")
                    ->timeout(60 * 50);
            }
        })->start()->wait();
    }

    public function insert(){
        $rand = rand(0,1000000000000);


        \DB::table("request_features")
            ->insert([
                "user_id" => 1,
                "name" => "halo".$rand,
                "description" => "des".$rand,
                "created_at" => now()->toDateTimeString(),
                "updated_at" => now()->toDateTimeString()
            ]);
    }
}
