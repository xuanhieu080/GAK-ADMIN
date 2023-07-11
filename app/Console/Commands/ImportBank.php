<?php

namespace App\Console\Commands;

use App\Models\Bank;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ImportBank extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-bank';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $response = Http::get('https://api.vietqr.io/v2/banks');
        $data = $response->json()['data'];
        $params = [];
        foreach ($data as $key => $value) {
            $params[] = [
                'name'       => $value['name'],
                'short_name' => $value['short_name'],
                'code'       => $value['code'],
                'logo'       => $value['logo'],
            ];
        }
        Bank::insert($params);
    }
}
