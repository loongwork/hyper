<?php

declare(strict_types=1);

namespace App\Http\Procedures;

use Sajya\Server\Procedure;

class TennisProcedure extends Procedure
{
    /**
     * The name of the procedure that will be
     * displayed and taken into account in the search
     */
    public static string $name = 'tennis';

    /**
     * Execute the procedure.
     */
    public function ping(): string
    {
        return 'pong';
    }
}
