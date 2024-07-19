<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

use Carbon\Carbon;
use App\Models\Inputorder;
use App\Models\Outputorder;

class PedidosChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'NÚMERO DE PEDIDOS POR MES';

    protected function getData(): array
    {

        $Product = $this->filters['IdProduct'] ?? null;
        $Area = $this->filters['IdArea'] ?? null;

        $data = $this->getProductsAndOutputsPerMonth($Product, $Area);
        return [
            'datasets' => [
                [
                    'label' => 'Número de Entradas Año Anterior',
                    'data' => $data['inputsPerMonthBack'],
                    'borderColor' => 'rgba(0,84,251,1)', // Verde
                    'backgroundColor' => 'rgba(0,84,251,1)', // Verde sólido
                    'hidden' => true, // Ocultar al cargar
                ],
                [
                    'label' => 'Número de Entradas Año Actual',
                    'data' => $data['inputsPerMonth'],
                    'borderColor' => 'rgba(75, 192, 192, 1)', // Verde
                    'backgroundColor' => 'rgba(75, 192, 192, 1)',
                ],
                [
                    'label' => 'Número de Consumos Año Anterior',
                    'data' => $data['outputsPerMonthBack'],
                    'borderColor' => 'rgba(250,82,238,1)', // Verde
                    'backgroundColor' => 'rgba(250,82,238,1)', // Verde sólido
                    'hidden' => true, // Ocultar al cargar
                ],
                [
                    'label' => 'Número de Consumos Año Actual',
                    'data' => $data['outputsPerMonth'],
                    'borderColor' => 'rgba(255, 99, 132, 1)', // Rojo
                    'backgroundColor' => 'rgba(255, 99, 132, 1)',
                ],
            ],
            'labels' => $data['months'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
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
            $productCount = Inputorder::whereMonth('created_at', $month)
                ->whereYear('created_at', $now->year)
                ->when(!is_null($areaFilter), fn($query) => $query->where('IdArea', $areaFilter)) // Filtra por IdArea si no es null
                ->when(!is_null($productFilter), fn($query) => $query->where('IdProduct', $productFilter)) // Filtra por IdProduct si no es null
                ->count();
            $inputsPerMonth[] = $productCount;

            $productCountBack = Inputorder::whereMonth('created_at', $month)
                ->whereYear('created_at', $nowBack->year)
                ->when(!is_null($areaFilter), fn($query) => $query->where('IdArea', $areaFilter)) // Filtra por IdArea si no es null
                ->when(!is_null($productFilter), fn($query) => $query->where('IdProduct', $productFilter)) // Filtra por IdProduct si no es null
                ->count();
            $inputsPerMonthBack[] = $productCountBack;

            $outputCount = Outputorder::whereMonth('created_at', $month)
                ->whereYear('created_at', $now->year)
                ->when(!is_null($areaFilter), fn($query) => $query->where('IdArea', $areaFilter)) // Filtra por IdArea si no es null
                ->when(!is_null($productFilter), fn($query) => $query->where('IdProduct', $productFilter)) // Filtra por IdProduct si no es null
                ->count();
            $outputsPerMonth[] = $outputCount;

            $outputCountBack = Outputorder::whereMonth('created_at', $month)
                ->whereYear('created_at', $nowBack->year)
                ->when(!is_null($areaFilter), fn($query) => $query->where('IdArea', $areaFilter)) // Filtra por IdArea si no es null
                ->when(!is_null($productFilter), fn($query) => $query->where('IdProduct', $productFilter)) // Filtra por IdProduct si no es null
                ->count();
            $outputsPerMonthBack[] = $outputCountBack;

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
