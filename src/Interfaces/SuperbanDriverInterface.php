<?php

namespace Emmanuelabraham\Superban\Interfaces;

use Illuminate\Http\Request;

interface SuperbanDriverInterface
{
    public function tooManyAttempts(Request $request, $maxAttempts, $decayMinutes);
    public function ban(Request $request, $banMinutes);
}