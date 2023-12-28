<?php

namespace Emmanuelabraham\Superban\Drivers;

use Illuminate\Http\Request;
use Illuminate\Redis\RedisManager;

class RedisDriver implements DriverInterface
{
    protected $redis;

    public function __construct(RedisManager $redis)
    {
        $this->redis = $redis;
    }

    public function tooManyAttempts(Request $request, $maxAttempts, $decayMinutes)
    {
        $ipAddress = $request->ip();
        $user = $request->user();
        $email = $user ? $user->email : null;

        $key = "attempts:ip:{$ipAddress}:email:{$email}";

        $attempts = $this->redis->get($key) ?? 0;

        if ($attempts >= $maxAttempts) {
            return true;
        }

        $this->redis->incr($key);
        $this->redis->expire($key, $decayMinutes * 60);

        return false;
    }

    public function ban(Request $request, $banMinutes)
    {
        $ipAddress = $request->ip();
        $user = $request->user();
        $email = $user ? $user->email : null;

        $key = "ban:ip:{$ipAddress}:email:{$email}";

        $this->redis->set($key, '1', 'EX', $banMinutes * 60);
    }
}