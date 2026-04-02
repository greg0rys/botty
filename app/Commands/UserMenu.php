<?php

namespace App\Commands;

use App\Models\Coins;
use App\Models\User;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class UserMenu extends Command
{
    protected $signature = 'botty:user-menu';
    protected $description = 'Display user options and manage currency';

public function handle()
{
    // 1. Fetch data once
    $u = User::with('coins')->get();

    $choice = $this->menu("Botty - User Menu")
        ->addOption("all", "Display All Active Users")
        ->addOption("balance", "Display User Coin Balance")
        ->addOption("give", "Give a User a Coin") // Added functionality
        ->open();

    if ($choice === "all") {
        $headers = ["ID", "First Name", "Last Name", "Email"];
        $rows = $u->map(fn($user) => [
            $user->id, 
            $user->first_name, 
            $user->last_name, 
            $user->email
        ])->toArray();
        $this->table($headers, $rows);
    } 

    if ($choice === "balance") {
        $headers = ["User ID", "User Name", "Coins"];
        $rows = $u->map(fn($user) => [
            $user->id,
            $user->first_name . ' ' . $user->last_name,
            $user->coins->coins ?? 0 
        ])->toArray();
        $this->table($headers, $rows);
    }

    if ($choice === "give") {
        // Create a simple list of names for the menu
        $userList = $u->pluck('first_name', 'id')->toArray();
        
        $targetId = $this->menu("Select User")
            ->addOptions($userList)
            ->open();

        if ($targetId) {
            $amount = $this->ask("How many coins?", 1);
            
            // Find or Create the record and save
            $record = Coins::firstOrCreate(['user_id' => $targetId]);
            $record->coins = ($record->coins ?? 0) + (int)$amount;
            $record->save();

            $this->info("Done. Added $amount coins to User #$targetId.");
        }
    }
}

    /**
     * Logic to select a user and increment their coins
     */
    private function giveCoinsAction($users)
    {
        // Create a selection list: [ID => "First Last"]
        $userList = $users->pluck('first_name', 'id')->toArray();

        $userId = $this->menu("Select User to Reward")
            ->addOptions($userList)
            ->open();

        if (!$userId)
            return;

        $amount = $this->ask("How many coins to give?", 10);

        if (!is_numeric($amount)) {
            $this->error("Invalid amount. Transaction cancelled.");
            return;
        }

        $this->task("Updating Ledger", function () use ($userId, $amount) {
            // Find record or create it if user has 0 coins currently
            $record = Coins::firstOrCreate(['user_id' => $userId]);
            $record->increment('coins', (int) $amount);
            return true;
        });

        $this->info("Successfully added $amount coins to User #$userId!");

        $this->notify("Coins Sent", "User #$userId received $amount coins.");
    }

    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}