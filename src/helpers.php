<?php

if (! function_exists("date_helper")) {
    /**
     * @return \Aweram\TraitsHelpers\Helpers\DateHelper
     */
    function date_helper(): \Aweram\TraitsHelpers\Helpers\DateHelper
    {
        return app("date_helper");
    }
}

// Example: num2word($count, ["товар", "товара", "товаров"])
if (! function_exists("num2word")) {
    function num2word($num, $words) {
        $num = $num % 100;
        if ($num > 19) $num = $num % 10;
        return match ($num) {
            1 => $words[0],
            2, 3, 4 => $words[1],
            default => $words[2],
        };
    }
}

if (! function_exists("size2word")) {
    function size2word($bytes): string
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }
}

if (! function_exists("read_csv")) {
    function read_csv($csvFile, $delimiter = ";"): array
    {
        $fileHandle = fopen($csvFile, "r");
        $lines = [];
        while (! feof($fileHandle)) {
            $lines[] = fgetcsv($fileHandle, 0, $delimiter);
        }
        fclose($fileHandle);
        return $lines;
    }
}

if (! function_exists("month2word")) {
    function month2word($month): string
    {
        return match ($month) {
            "01" => "января",
            "02" => "февраля",
            "03" => "марта",
            "04" => "апреля",
            "05" => "мая",
            "06" => "июня",
            "07" => "июля",
            "08" => "августа",
            "09" => "сентября",
            "10" => "октября",
            "11" => "ноября",
            "12" => "декабря",
            default => $month,
        };
    }
}
