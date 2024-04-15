<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\User;
use App\Models\Variant;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StatisticController extends Controller
{

    public function index(Request $request) {
        $orders = Bill::query();
        $result = [];
        switch ($request->by){
            case "day":
                $labels = [[0, 3], [3, 6], [6, 9], [9, 12], [12, 15], [15, 18], [18, 21], [21, 24]];
                foreach ($labels as $label)  {
                    $startHour = $label[0];
                    $endHour = $label[1] - 1;

                    $result[$startHour . ":00" . " - " . $endHour . ":59h"] = Bill::query()->whereBetween('created_at', [
                        Carbon::today()->startOfHour()->addHours($startHour),
                        Carbon::today()->startOfHour()->addHours($endHour)->addMinutes(59)->addSeconds(59)
                    ])->count();
                }
                break;
            case "week":
                $startOfWeek = Carbon::now()->startOfWeek();

                for ($i = 0; $i < 7; $i++) {
                    $startDate = $startOfWeek->copy()->addDays($i);
                    $endDate = $startDate->copy()->endOfDay();

                    $result[$startDate->toDateString()] = Bill::query()->whereBetween('created_at', [$startDate, $endDate])->count();
                }
                break;


            case "month":
                $month = Carbon::now()->month;
                $startOfMonth = Carbon::today()->startOfMonth();
                $endOfMonth = Carbon::now()->endOfMonth();

                $labels = [
                    [1, 5],
                    [6, 10],
                    [11, 15],
                    [16, 20],
                    [21, 25],
                    [26, $endOfMonth->day],
                ];

                foreach ($labels as $label) {
                    $startDate = $startOfMonth->copy()->addDays($label[0] - 1);
                    $endDate = $startOfMonth->copy()->addDays($label[1])->endOfDay();

                    $result[$label[0] . "/$month" . " - " . $label[1] . "/$month"] = Bill::query()
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->count();
                }
                break;

            case "year":
                $startOfYear = Carbon::now()->startOfYear();
                $labels = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];

                foreach ($labels as $month) {
                    $startDate = Carbon::create($startOfYear->year, $month, 1)->startOfMonth();
                    $endDate = $startDate->copy()->endOfMonth();
                    $result["Tháng " . $month] = Bill::query()
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->count();
                }
                break;

        }
        return \response()->json($result, 200);
    }

    public function orderToday() {
        $orders = Bill::query()->whereDate('created_at', Carbon::today());
        $sales = $orders->get()->reduce(function ($total, $order){
            return $total + $order->total_money;
        }, 0);
        $total_orders = $orders->count();
        $variants = Variant::all()->count();
        $users = User::all()->count();
        return \response()->json([
            "order_today" => $total_orders,
            "products" => $variants,
            "users" => $users,
            "sales" => $sales
        ], 200);
    }
}
