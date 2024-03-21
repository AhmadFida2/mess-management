<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendAttendanceSheet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sheet:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends printable Attendance Sheet to admins';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

    }
}
