<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTO\ReportFilterDTO;
use App\Exports\CategoryReportExport;
use App\Exports\OperatorReportExport;
use App\Exports\RegionReportExport;
use App\Exports\WasherReportExport;
use App\Services\RegionService;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService,
        protected RegionService $regionService,
    ) {}

    public function category(Request $request): View
    {
        $filter = $this->buildFilter($request);
        $data = $this->reportService->getByCategory($filter);
        $regions = $this->regionService->listActive();

        return view('reports.category', [
            'data' => $data,
            'regions' => $regions,
            'filter' => $filter,
            'totalOrders' => $data->sum('orders_count'),
            'totalAmount' => $data->sum('total_amount'),
        ]);
    }

    public function exportCategory(Request $request): BinaryFileResponse
    {
        $filter = $this->buildFilter($request);
        $data = $this->reportService->getByCategory($filter);

        return Excel::download(new CategoryReportExport($data), 'hisobot-kategoriyalar-'.now()->format('Y-m-d').'.xlsx');
    }

    public function region(Request $request): View
    {
        $filter = $this->buildFilter($request);
        $data = $this->reportService->getByRegion($filter);
        $regions = $this->regionService->listActive();

        return view('reports.region', [
            'data' => $data,
            'regions' => $regions,
            'filter' => $filter,
            'totalOrders' => $data->sum('orders_count'),
            'totalAmount' => $data->sum('total_amount'),
        ]);
    }

    public function exportRegion(Request $request): BinaryFileResponse
    {
        $filter = $this->buildFilter($request);
        $data = $this->reportService->getByRegion($filter);

        return Excel::download(new RegionReportExport($data), 'hisobot-filiallar-'.now()->format('Y-m-d').'.xlsx');
    }

    public function operator(Request $request): View
    {
        $filter = $this->buildFilter($request);
        $data = $this->reportService->getByOperator($filter);
        $regions = $this->regionService->listActive();

        return view('reports.operator', [
            'data' => $data,
            'regions' => $regions,
            'filter' => $filter,
            'totalOrders' => $data->sum('orders_count'),
            'totalAmount' => $data->sum('total_amount'),
        ]);
    }

    public function exportOperator(Request $request): BinaryFileResponse
    {
        $filter = $this->buildFilter($request);
        $data = $this->reportService->getByOperator($filter);

        return Excel::download(new OperatorReportExport($data), 'hisobot-operatorlar-'.now()->format('Y-m-d').'.xlsx');
    }

    public function washer(Request $request): View
    {
        $filter = $this->buildFilter($request);
        $data = $this->reportService->getByWasher($filter);
        $regions = $this->regionService->listActive();

        return view('reports.washer', [
            'data' => $data,
            'regions' => $regions,
            'filter' => $filter,
            'totalOrders' => $data->sum('orders_count'),
            'totalAmount' => $data->sum('total_amount'),
        ]);
    }

    public function exportWasher(Request $request): BinaryFileResponse
    {
        $filter = $this->buildFilter($request);
        $data = $this->reportService->getByWasher($filter);

        return Excel::download(new WasherReportExport($data), 'hisobot-yuvuvchilar-'.now()->format('Y-m-d').'.xlsx');
    }

    protected function buildFilter(Request $request): ReportFilterDTO
    {
        return new ReportFilterDTO(
            dateFrom: $request->input('date_from'),
            dateTo: $request->input('date_to'),
            regionId: $request->filled('region_id') ? (int) $request->input('region_id') : null,
        );
    }
}
