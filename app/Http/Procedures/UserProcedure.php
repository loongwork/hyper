<?php

declare(strict_types=1);

namespace App\Http\Procedures;

use App\Models\User;
use Sajya\Server\Procedure;

class UserProcedure extends Procedure
{
    /**
     * The name of the procedure that will be
     * displayed and taken into account in the search
     */
    public static string $name = 'user';

    /**
     * Find a user by its ID
     */
    public function find(string $id): ?User
    {
        return User::find($id);
    }

    /**
     * Find a user by its username
     */
    public function findByUsername(string $username): ?User
    {
        return User::firstWhere('username', $username);
    }
}
