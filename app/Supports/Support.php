<?php

namespace App\Supports;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class Support
{
    public static final function genCode($table, $column, $length = 10)
    {
        do {
            $code = Str::random($length);
        } while (DB::table("$table")->where("$column", $code)->first());

        return $code;
    }

    public static final function generateIntUnique($table, $column, $min = 100000, $max = 99999999)
    {
        do {
            $code = random_int($min, $max);
        } while (DB::table("$table")->where("$column", $code)->first());

        return $code;
    }
}
