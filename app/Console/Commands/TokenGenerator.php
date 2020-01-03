<?php


namespace Looden\Framework\Console\Commands;


use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class TokenGenerator extends Command
{
    protected $signature = 'looden:token
                        {userid : The ID of the user}';
    public function handle(){
        $userId = $this->argument('userid');
        $user = User::find($userId);
        $token = Str::random(60);

        $user->api_token = config('auth.guards.api.hash')
            ? $user->api_token = hash('sha256', $token)
            : $user->api_token = $token;

        $user->save();
        $this->info($token);
    }
}