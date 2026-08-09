<?php

namespace App\Services\Invitations;

use App\Models\Invitation;
use Illuminate\Support\Str;

class InvitationCodeGenerator
{
    public function generate(int $length = 8): string
    {
        do {
            $code = Str::upper(Str::random($length));
        } while (Invitation::query()->where('code', $code)->exists());

        return $code;
    }
}
