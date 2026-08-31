<?php

namespace App\BookShop\Http\Controllers\Staff;

use App\BookShop\Http\Controllers\Controller;
use App\BookShop\Models\Staff;
use App\BookShop\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportController extends Controller
{
    public function __construct(private readonly ReportService $reports)
    {
    }

    public function index(Request $request): View
    {
        /** @var Staff $staff */
        $staff = Auth::guard('bookshop_staff')->user();

        [$from, $to] = $this->resolveRange($request);
        $data = $this->reports->summary($staff, $from, $to);

        return view('bookshop::staff.reports.index', array_merge($data, [
            'staff' => $staff,
            'from' => $from,
            'to' => $to,
        ]));
    }

    public function export(Request $request): Response
    {
        /** @var Staff $staff */
        $staff = Auth::guard('bookshop_staff')->user();

        [$from, $to] = $this->resolveRange($request);
        $data = $this->reports->summary($staff, $from, $to);

        $spreadsheet = new Spreadsheet;

        $summary = $spreadsheet->getActiveSheet();
        $summary->setTitle('Summary');
        $summary->fromArray(['Metric', 'Value'], null, 'A1');
        $summary->fromArray([
            ['Period', $from->format('Y-m-d').' to '.$to->format('Y-m-d')],
            ['Total Revenue (GHS)', number_format($data['total_revenue'], 2)],
            ['Total Orders', $data['total_orders']],
            ['Average Order Value (GHS)', number_format($data['average_order_value'], 2)],
        ], null, 'A2');

        $books = $spreadsheet->createSheet();
        $books->setTitle('Top Books');
        $books->fromArray(['Book', 'Qty Sold', 'Revenue (GHS)'], null, 'A1');
        $row = 2;
        foreach ($data['top_books'] as $item) {
            $books->fromArray([$item->book?->title ?? 'Unknown', $item->qty_sold, number_format($item->revenue, 2)], null, "A{$row}");
            $row++;
        }

        $daily = $spreadsheet->createSheet();
        $daily->setTitle('Daily');
        $daily->fromArray(['Date', 'Revenue (GHS)', 'Orders'], null, 'A1');
        $row = 2;
        foreach ($data['daily'] as $day) {
            $daily->fromArray([$day->day, number_format($day->revenue, 2), $day->orders], null, "A{$row}");
            $row++;
        }

        if ($staff->isSuperAdmin() && $data['by_branch']->isNotEmpty()) {
            $byBranch = $spreadsheet->createSheet();
            $byBranch->setTitle('By Branch');
            $byBranch->fromArray(['Branch', 'Revenue (GHS)', 'Orders'], null, 'A1');
            $row = 2;
            foreach ($data['by_branch'] as $branchRow) {
                $byBranch->fromArray([$branchRow->branch_name, number_format($branchRow->revenue, 2), $branchRow->orders], null, "A{$row}");
                $row++;
            }
        }

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        $filename = 'bookshop-sales-'.$from->format('Ymd').'-'.$to->format('Ymd').'.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    private function resolveRange(Request $request): array
    {
        $from = $request->filled('from')
            ? Carbon::parse($request->string('from'))->startOfDay()
            : now()->startOfMonth();

        $to = $request->filled('to')
            ? Carbon::parse($request->string('to'))->endOfDay()
            : now()->endOfDay();

        return [$from, $to];
    }
}
