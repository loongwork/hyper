<?php

declare(strict_types=1);

namespace App\Http\Procedures;

use App\Models\PlayerProfile;
use App\Models\VerificationCode;
use Illuminate\Http\Request;
use Sajya\Server\Procedure;

class AccountLinkProcedure extends Procedure
{
    /**
     * The name of the procedure that will be
     * displayed and taken into account in the search
     */
    public static string $name = 'accountLink';

    /**
     * Get verification code.
     */
    public function getVerificationCode(Request $request): string
    {
        $username = $request->input('username');

        return VerificationCode::createFor($username);
    }

    /**
     * Link account.
     */
    public function linkAccount(Request $request): bool
    {
        $username = $request->input('username');
        $code = $request->input('code');
        $qq = $request->input('qq');

        if (VerificationCode::verify($username, $code)) {
            $profile = PlayerProfile::whereUsername($username)->firstOrFail();

            if ($profile->user && isset($profile->user[0])) {
                $profile->user[0]->qq = $qq;
                $profile->user[0]->save();
            } else {
                $profile->user()->create([
                    'username' => $username,
                    'password' => $qq,
                    'qq' => $qq,
                    'activated_at' => now(),
                ]);
            }

            return true;
        }

        return false;
    }
}
