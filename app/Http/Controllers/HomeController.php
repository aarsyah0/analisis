<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class HomeController extends Controller
{
    public function home()
    {
        return redirect('dashboard');
    }

    public function wordcloudData(Request $request)
    {
        $source = $request->query('source', 'gopay'); // default 'gopay'

        // Tentukan file berdasarkan sumber
        if ($source === 'gopay') {
            $files = ['gopaylabel.csv'];
        } elseif ($source === 'dana') {
            $files = ['danalabel.csv'];
        } elseif($source === 'shopeepay'){
            $files = ['shopeepaylabel.csv'];
        } else {
            $files = ['gopaylabel.csv', 'danalabel.csv', 'shopeepaylabel.csv'];
        }

        $allText = '';
    foreach ($files as $file) {
        $path = resource_path("views/{$file}");
        if (File::exists($path)) {
            // Baca baris per baris
            $csv = array_map('str_getcsv', File::lines($path)->toArray());
            $header = array_shift($csv);
            $idx    = array_search('cleaned_text', $header);
            foreach ($csv as $row) {
                if (isset($row[$idx])) {
                    $allText .= ' '.strtolower($row[$idx]);
                }
            }
        }
    }
        $words    = str_word_count($allText, 1);
        $filtered = array_filter($words, fn($w) => strlen($w) > 2);
        $counts   = array_count_values($filtered);
        arsort($counts);
        $top100   = array_slice($counts, 0, 100, true);

        $list = [];
        foreach ($top100 as $word => $count) {
            $list[] = [$word, $count];
        }

        return response()->json($list);
    }
}
