<?php

namespace App\Commands;

use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class MainMenu extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'botty:main-menu';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load the main operation menu for the program';
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $choice = $this->menu('BOTTY Status: Online', [
            'Status' => 'Get Bot Uptime Stats',
            'users' => 'Manage Server Users',
            'System Time' => 'Display Server Time',
            'exit' => 'Take Botty Offline',
        ])->open();

        // Handle the logic
        if ($choice === 'hello') {
            $this->newLine();
            $this->task("Authenticating User", function () {
                sleep(2);
                return true;
            });
            $this->info("\nBotty is online and connected to the guild as of: " . Carbon::now('PST')->format("g:i a m-d-y"));
        } elseif ($choice === 'users') {
            $this->warn("\n[SYSTEM TIME]: " . Carbon::now('PST')->format('m-d-y g:i a'));

        } else {
            $this->task("Shutting down Botty", function () {
                sleep(2);
                return false;
            });
        }

        $this->table(["Queef"], ["Greg"]);
    }
    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }

    private function handle_system_status_request(): void
    {

    }
}

