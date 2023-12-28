<?php

namespace Emmanuelabraham\Superban\Drivers;

use Illuminate\Http\Request;
use Illuminate\Database\DatabaseManager;
use Emmanuelabraham\Superban\Interfaces\DriverInterface;

class DatabaseDriver implements DriverInterface
{
    protected $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function tooManyAttempts(Request $request, $maxAttempts, $decayMinutes)
    {
        $ipAddress = $request->ip();
        $user = $request->user();
        $email = $user ? $user->email : null;

        $attempts = $this->db->table('superban_attempts')
            ->where('ip_address', $ipAddress)
            ->where('email', $email)
            ->where('created_at', '>=', now()->subMinutes($decayMinutes))
            ->count();

        return $attempts >= $maxAttempts;
    }

    public function ban(Request $request, $banMinutes)
    {
        $ipAddress = $request->ip();
        $user = $request->user();
        $email = $user ? $user->email : null;

        $this->db->table('superban_bans')->insert([
            'ip_address' => $ipAddress,
            'email' => $email,
            'banned_until' => now()->addMinutes($banMinutes),
        ]);
    }
}