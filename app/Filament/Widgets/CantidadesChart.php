<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

use Carbon\Carbon;
use App\Models\Inputorder;
use App\Models\Outputorder;

class CantidadesChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'CANTIDADES POR MES';

    protected function getData(): array
    {
        $Product = $this->filters['IdProduct'] ?? null;
        $Area = $this->filters['IdArea'] ?? null;

        $data = $this->getProductsAndOutputsPerMonth($Product, $Area);
        return [
            'datasets' => [
                [
                    'label' => 'Cantidad Pedida Año Anterior',
                    'data' => $data['inputsPerMonthBack'],
                    'borderColor' => 'rgba(52, 144, 34, 0.5)', // Verde
                    'backgroundColor' => 'rgba(52, 144, 34, 0.5)', // Verde sólido
                    'hidden' => true, // Ocultar al cargar
                ],
                [
                    'label' => 'Cantidad Pedida Año Actual',
                    'data' => $data['inputsPerMonth'],
                    'borderColor' => 'rgba(52, 144, 34, 1)', // Verde
                    'backgroundColor' => 'rgba(52, 144, 34, 1)', // Verde sólido
                ],
                [
                    'label' => 'Cantidad Consumida Año Anterior',
                    'data' => $data['outputsPerMonthBack'],
                    'borderColor' => 'rgba(175, 6, 32, 0.5)', // Rojo
                    'backgroundColor' => 'rgba(175, 6, 32, 0.5)', // Rojo sólido
                    'hidden' => true, // Ocultar al cargar
                ],
                [
                    'label' => 'Cantidad Consumida Año Actual',
                    'data' => $data['outputsPerMonth'],
                    'borderColor' => 'rgba(175, 6, 32, 1)', // Rojo
                    'backgroundColor' => 'rgba(175, 6, 32, 1)', // Rojo sólido
                ],
            ],
            'labels' => $data['months'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getProductsAndOutputsPerMonth($productFilter, $areaFilter): array
    {
        $now = Carbon::now();
        $nowBack = Carbon::now()->subYear();

        $inputsPerMonth = [];
        $outputsPerMonth = [];
        $inputsPerMonthBack = [];
        $outputsPerMonthBack = [];

        $months = collect(range(1, 12))->map(function ($month) use ($now, $nowBack, $areaFilter, $productFilter, &$inputsPerMonth, &$outputsPerMonth, &$inputsPerMonthBack, &$outputsPerMonthBack) {
            $productSum = Inputorder::whereMonth('created_at', $month)
                ->whereYear('created_at', $now->year)
                ->when(!is_null($areaFilter), fn($query) => $query->where('IdArea', $areaFilter)) // Filtra por IdArea si no es null
                ->when(!is_null($productFilter), fn($query) => $query->where('IdProduct', $productFilter)) // Filtra por IdProduct si no es null
                ->sum('Quantity');
            $inputsPerMonth[] = $productSum;

            $outputSum = Outputorder::whereMonth('created_at', $month)
                ->whereYear('created_at', $now->year)
                ->when(!is_null($areaFilter), fn($query) => $query->where('IdArea', $areaFilter)) // Filtra por IdArea si no es null
                ->when(!is_null($productFilter), fn($query) => $query->where('IdProduct', $productFilter)) // Filtra por IdProduct si no es null
                ->sum('Quantity');
            $outputsPerMonth[] = $outputSum;

            $productSumBack = Inputorder::whereMonth('created_at', $month)
                ->whereYear('created_at', $nowBack->year)
                ->when(!is_null($areaFilter), fn($query) => $query->where('IdArea', $areaFilter)) // Filtra por IdArea si no es null
                ->when(!is_null($productFilter), fn($query) => $query->where('IdProduct', $productFilter)) // Filtra por IdProduct si no es null
                ->sum('Quantity');
            $inputsPerMonthBack[] = $productSumBack;

            $outputSumBack = Outputorder::whereMonth('created_at', $month)
                ->whereYear('created_at', $nowBack->year)
                ->when(!is_null($areaFilter), fn($query) => $query->where('IdArea', $areaFilter)) // Filtra por IdArea si no es null
                ->when(!is_null($productFilter), fn($query) => $query->where('IdProduct', $productFilter)) // Filtra por IdProduct si no es null
                ->sum('Quantity');
            $outputsPerMonthBack[] = $outputSumBack;

            return $now->month($month)->format('M');
        })->toArray();

        return [
            'inputsPerMonth' => $inputsPerMonth,
            'inputsPerMonthBack' => $inputsPerMonthBack,
            'outputsPerMonth' => $outputsPerMonth,
            'outputsPerMonthBack' => $outputsPerMonthBack,
            'months' => $months,
        ];
    }
}
