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

    public static final function writeJsonFile($filePath, $newData)
    {

        // Chuyển đổi dữ liệu thành định dạng JSON
        $jsonContent = json_encode($newData, JSON_PRETTY_PRINT);

        try {
            // Kiểm tra xem file có tồn tại không
            if (file_exists($filePath)) {
                // Nếu file đã tồn tại, ghi đè dữ liệu trong file đó
                file_put_contents($filePath, $jsonContent);
            } else {
                // Nếu file chưa tồn tại, tạo file mới và ghi dữ liệu vào đó
                file_put_contents($filePath, $jsonContent);
            }

//            return response()->json(['message' => 'File has been written successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error writing file', 'error' => $e->getMessage()], 500);
        }
    }
}
