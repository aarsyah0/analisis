<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class HomeController extends Controller
{
    /**
     * Display the dashboard with sentiment counts.
     */
    public function index()
    {
        // Paths to CSV files in public/storage
        $pathDana   = public_path('storage/data_labeled.csv');
        $pathGopay  = public_path('storage/data_labeledgopay.csv');
        $pathShopee = public_path('storage/data_labeledshopee.csv');

        // Read CSVs
        $danaRows   = $this->readCsv($pathDana);
        $gopayRows  = $this->readCsv($pathGopay);
        $shopeeRows = $this->readCsv($pathShopee);

        // Merge for "all"
        $allRows = array_merge($danaRows, $gopayRows, $shopeeRows);

        // Counting helper
        $calcCounts = function(array $rows) {
            $counts = ['positif' => 0, 'netral' => 0, 'negatif' => 0];
            $map    = [
                'positive' => 'positif',
                'neutral'  => 'netral',
                'negative' => 'negatif',
            ];

            foreach ($rows as $r) {
                $label = strtolower(trim($r['label_auto'] ?? ''));
                if (isset($map[$label])) {
                    $counts[$map[$label]]++;
                }
            }

            return $counts;
        };

        // Calculate counts
        $counts = [
            'all'       => $calcCounts($allRows),
            'dana'      => $calcCounts($danaRows),
            'gopay'     => $calcCounts($gopayRows),
            'shopeepay' => $calcCounts($shopeeRows),
        ];

        return view('dashboard', compact('counts'));
    }

    /**
     * Return JSON word-cloud data for a given source.
     * GET /wordcloud-data?source=all|dana|gopay|shopeepay
     */
    public function wordcloudData(Request $request)
{
    $source    = strtolower($request->query('source', 'all'));
    $sentiment = strtolower($request->query('sentiment', 'all'));
    $map       = ['positif'=>'positive','netral'=>'neutral','negatif'=>'negative'];

    // Ambil semua baris sesuai source (sama seperti sebelumnya)
    switch ($source) {
        case 'dana':
            $rows = $this->readCsv(public_path('storage/data_labeled.csv'));
            break;
        case 'gopay':
            $rows = $this->readCsv(public_path('storage/data_labeledgopay.csv'));
            break;
        case 'shopeepay':
            $rows = $this->readCsv(public_path('storage/data_labeledshopee.csv'));
            break;
        default:
            $rows = array_merge(
                $this->readCsv(public_path('storage/data_labeled.csv')),
                $this->readCsv(public_path('storage/data_labeledgopay.csv')),
                $this->readCsv(public_path('storage/data_labeledshopee.csv'))
            );
    }

    // Jika permintaan untuk sentimen tertentu, filter dulu
    if (isset($map[$sentiment])) {
        $engLabel = $map[$sentiment];
        $rows = array_filter($rows, fn($r) => strtolower($r['label_auto'] ?? '') === $engLabel);
    }

    // Tokenize & hitung frekuensi kata
    $freq = [];
    foreach ($rows as $row) {
        $words = preg_split('/\W+/', strtolower($row['clean_text'] ?? ''), -1, PREG_SPLIT_NO_EMPTY);
        foreach ($words as $w) {
            if (strlen($w) < 3) continue;
            $freq[$w] = ($freq[$w] ?? 0) + 1;
        }
    }

    arsort($freq);
    $top = array_slice($freq, 0, 800, true);  // batasi ke 70 kata

    $list = [];
    foreach ($top as $word => $count) {
        $list[] = [$word, $count];
    }

    return Response::json($list);
}


    /**
     * Helper: load a CSV into an associative array.
     */
    private function readCsv(string $path): array
    {
        if (! File::exists($path)) {
            logger()->warning("CSV file not found at {$path}");
            return [];
        }

        $handle = fopen($path, 'r');
        $header = fgetcsv($handle);
        $data   = [];

        while ($row = fgetcsv($handle)) {
            $data[] = array_combine($header, $row);
        }

        fclose($handle);
        return $data;
    }
}
