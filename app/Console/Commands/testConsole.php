<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Dispensing;
use App\Classes\Yoga;
use App\Pasien;
use App\Periksa;
use App\TransaksiPeriksa;
use Log;
use App\Http\Controllers\CustomController;

class testConsole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:console';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perintah untuk test console shell script';

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
		dd(session('antrian_id'));
    }
}
