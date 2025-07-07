<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Models
use App\Models\log;

class HomeController extends Controller
{
    /**
     * Display home page.
     *
     * @return view()
     */
    public function index()
    {
        return view('admin.dashboard');
    }

    public function dashboard_dummy_data(Request $request)
    {
        $query = log::whereNotNull('created_at');

        if ($request->daterange) {
            // DATERANGE FORMATING
            $daterange = explode(' - ', $request->daterange);
            $start_date_plain = explode('/', $daterange[0]);
            $end_date_plain = explode('/', $daterange[1]);
            $start_date = $start_date_plain[2] . '-' . $start_date_plain[1] . '-' . $start_date_plain[0];
            $end_date = $end_date_plain[2] . '-' . $end_date_plain[1] . '-' . $end_date_plain[0];

            // DATERANGE QUERY
            $query->whereBetween('created_at', [$start_date, $end_date]);
        } else {
            switch ($request->period) {
                case 'monthly':
                    $start_date = date('Y-m-d', strtotime('-12 months'));
                    $query->where('created_at', '>=', $start_date);
                    break;

                case 'annual':
                    // NO NEED - default get all data
                    break;

                default:
                    # daily
                    $start_date = date('Y-m-d', strtotime('-30 days'));
                    $query->where('created_at', '>=', $start_date);
                    break;
            }
        }

        switch ($request->period) {
            case 'monthly':
                $query->select(
                    DB::raw("GROUP_CONCAT(DISTINCT (CONCAT(YEAR(created_at), ' ', MONTHNAME(created_at)))) AS period"),
                    // DB::raw("COUNT(IF(status = 'waiting', 1, NULL)) AS Waiting"),
                    // DB::raw("COUNT(IF(status = 'approve', 1, NULL)) AS Approved"),
                    // DB::raw("COUNT(IF(status = 'reject', 1, NULL)) AS Rejected"),
                    // DB::raw("COUNT(IF(status = 'dikirim', 1, NULL)) AS Shipping"),
                    // DB::raw("COUNT(IF(status = 'selesai', 1, NULL)) AS Done"),
                    DB::raw("COUNT(*) AS Total")
                );
                $query->groupBy(DB::raw("YEAR(created_at)"));
                $query->groupBy(DB::raw("MONTH(created_at)"));
                break;

            case 'annual':
                $query->select(
                    DB::raw("YEAR(created_at) AS period"),
                    // DB::raw("COUNT(IF(status = 'waiting', 1, NULL)) AS Waiting"),
                    // DB::raw("COUNT(IF(status = 'approve', 1, NULL)) AS Approved"),
                    // DB::raw("COUNT(IF(status = 'reject', 1, NULL)) AS Rejected"),
                    // DB::raw("COUNT(IF(status = 'dikirim', 1, NULL)) AS Shipping"),
                    // DB::raw("COUNT(IF(status = 'selesai', 1, NULL)) AS Done"),
                    DB::raw("COUNT(*) AS Total")
                );
                $query->groupBy(DB::raw("YEAR(created_at)"));
                break;

            default:
                # daily
                $query->select(
                    DB::raw("DATE(created_at) AS period"),
                    // DB::raw("COUNT(IF(status = 'waiting', 1, NULL)) AS Waiting"),
                    // DB::raw("COUNT(IF(status = 'approve', 1, NULL)) AS Approved"),
                    // DB::raw("COUNT(IF(status = 'reject', 1, NULL)) AS Rejected"),
                    // DB::raw("COUNT(IF(status = 'dikirim', 1, NULL)) AS Shipping"),
                    // DB::raw("COUNT(IF(status = 'selesai', 1, NULL)) AS Done"),
                    DB::raw("COUNT(*) AS Total")
                );
                $query->groupBy(DB::raw("DATE(created_at)"));
                break;
        }

        $data = $query->get();

        // dd($data);

        if ($request->export) {
            // SET FILE NAME
            $filename = date('YmdHis') . '-dummy_data';

            // return Excel::download(new RedeemDataExportView($data), $filename . '.xlsx');
        }

        // setup data for chart
        $legends = [
            'Total'
        ];
        $series = [];
        $periods = [];

        $container = [];

        foreach ($data as $item) {
            foreach ($legends as $legend) {
                $container[$legend][$item->period] = $item->$legend;
            }

            // Build unique dates array
            if (!in_array($item->period, $periods)) {
                $periods[] = $item->period;
            }
        }

        foreach ($container as $key => $value) {
            $dataset = [];
            foreach ($periods as $period) {
                $dataset[] = isset($value[$period]) ? $value[$period] : 0;
            }

            $series[] = [
                'name' => $key,
                'type' => 'line',
                'smooth' => true,
                'itemStyle' => [
                    'normal' => [
                        'areaStyle' => [
                            'type' => 'default'
                        ]
                    ]
                ],
                'data' => $dataset
            ];
        }

        $response = [
            'legends' => $legends,
            'periods' => $periods,
            'series' => $series
        ];

        return response()->json($response);
    }
}
