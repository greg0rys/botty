<?php
namespace App\Helpers;

use App\Models\User;
use App\Models\Coins;

class CoinManager
{
    public $s =collect(User::all());
    public $c = collect(Coins::all());
    public function add_coins(int $id=1, int $amount=0)
    {
        $user = Coins::firstOrCreate(["user_id"=> $id,"amount"=> $amount]);
        $total_coins = $user->coins;
        $user->coins = ($total_coins ?? 0) + $amount;
    }
}