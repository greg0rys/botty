<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use Illuminate\Database\Eloquent\Model;

class Coins extends Model
{
    /**
     * The name and signature of the console command.
     * {user?} and {amount?} are optional arguments.
     */
    protected $signature = 'botty:user-menu {user? : The ID of the user} {amount? : The number of coins}';

    /**
     * The console command description.
     */
    protected $description = 'Manage users and grant coins via menu or direct arguments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user');
        $amount = $this->argument('amount');

        $this->task("Granting coins to " . $userId ?? "Null", function () use ($userId, $amount) {
            if ($userId !== null && $amount !== null) {
                $this->grantCoins((int) $userId, (int) $amount);
                return true;
            } else {
                return false;
            }
        });


        // 2. Interactive Mode (No arguments passed)
        $u = User::with('coins')->get();

        $choice = $this->menu("Botty - User Menu")
            ->addOption("all", "Display All Active Users")
            ->addOption("balance", "Display User Coin Balance")
            ->addOption("give", "Give a User a Coin")
            ->open();

        if ($choice === "all") {
            $this->table(
                ["ID", "First Name", "Last Name", "Email"],
                $u->map(fn($user) => [$user->id, $user->first_name, $user->last_name, $user->email])->toArray()
            );
        }

        if ($choice === "balance") {
            $this->task("Loading Coin Balances", function () {
                sleep(2);
                return true;
            });

            $this->table(
                ["User ID", "User Name", "Coins"],
                $u->map(fn($user) => [$user->id, $user->first_name . ' ' . $user->last_name, $user->coins->coins ?? 0])->toArray()
            );
        }

        if ($choice === "give") {
            // Select user from the existing $u collection
            $userList = $u->pluck('first_name', 'id')->toArray();
            $targetId = $this->menu("Select User")->addOptions($userList)->open();

            if ($targetId) {
                $amt = (int) $this->ask("How many coins?", 1);
                $this->grantCoins($targetId, $amt);
            }
        }
    }

    /**
     * Internal logic to update the database.
     */
    private function grantCoins($id, $amount)
    {
        $this->task("Updating Ledger for User #$id", function () use ($id, $amount) {
            $record = Coins::firstOrCreate(['user_id' => $id]);
            $record->coins = ($record->coins ?? 0) + $amount;
            return $record->save();
        });

        $this->info("Successfully granted $amount coins.");
    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}