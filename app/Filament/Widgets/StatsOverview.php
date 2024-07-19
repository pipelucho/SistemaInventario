<?php

namespace App\Filament\Widgets;

use App\Models\Inputorder;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Filters\Filter;


class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected function getStats(): array
    {
        $productFilter = $this->filters['IdProduct'] ?? null;
        $areaFilter = $this->filters['IdArea'] ?? null;

        $start = $this->filters['startDate'] ?? null;
        $end = $this->filters['endDate'] ?? null;
        
        

        // Realiza la consulta para contar órdenes con Status = False y aplica filtros
        $totalSinRecibir = Inputorder::where('Status', false)
            ->when(!is_null($areaFilter), fn($query) => $query->where('IdArea', $areaFilter))
            ->when(!is_null($productFilter), fn($query) => $query->where('IdProduct', $productFilter))
            ->when(!is_null($start), fn($query) => $query->whereDate('created_at', '>=', $start))
            ->when(!is_null($end), fn($query) => $query->whereDate('created_at', '<=', $end))
            ->count();

        // Realiza la consulta para contar órdenes con Status = True y aplica filtros
        $totalRecibidos = Inputorder::where('Status', true)
            ->when(!is_null($areaFilter), fn($query) => $query->where('IdArea', $areaFilter))
            ->when(!is_null($productFilter), fn($query) => $query->where('IdProduct', $productFilter))
            ->when(!is_null($start), fn($query) => $query->whereDate('created_at', '>=', $start))
            ->when(!is_null($end), fn($query) => $query->whereDate('created_at', '<=', $end))
            ->count();

        $totalPedidos = $totalRecibidos + $totalSinRecibir;

        // Realiza la consulta para sumar cantidades con Status = False y aplica filtros
        $cantidadSinRecibir = Inputorder::where('Status', false)
            ->when(!is_null($areaFilter), fn($query) => $query->where('IdArea', $areaFilter))
            ->when(!is_null($productFilter), fn($query) => $query->where('IdProduct', $productFilter))
            ->when(!is_null($start), fn($query) => $query->whereDate('created_at', '>=', $start))
            ->when(!is_null($end), fn($query) => $query->whereDate('created_at', '<=', $end))
            ->sum('Quantity');

        // Realiza la consulta para sumar cantidades con Status = True y aplica filtros
        $cantidadRecibidos = Inputorder::where('Status', true)
            ->when(!is_null($areaFilter), fn($query) => $query->where('IdArea', $areaFilter))
            ->when(!is_null($productFilter), fn($query) => $query->where('IdProduct', $productFilter))
            ->when(!is_null($start), fn($query) => $query->whereDate('created_at', '>=', $start))
            ->when(!is_null($end), fn($query) => $query->whereDate('created_at', '<=', $end))
            ->sum('Quantity');

        $cantidadPedidos = $cantidadRecibidos + $cantidadSinRecibir;
        return [
            Stat::make('Total Pedidos', $totalPedidos)
                ->description('Pedidos realizados')
                ->descriptionIcon('heroicon-m-arrow-trending-up'),

            Stat::make('Total Pedidos', $totalSinRecibir)
                ->description('Pedidos Pendientes por recibir')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3]),

            Stat::make('Total Pedidos', $totalRecibidos)
                ->description('Pedidos recibidos')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3]),


            Stat::make('Cantidad Pedidos', $cantidadPedidos)
                ->description('Cantidad Pedida')
                ->descriptionIcon('heroicon-m-arrow-trending-up'),

            Stat::make('Cantidad Pedidos', $cantidadSinRecibir)
                ->description('Cantidad Pendientes por recibir')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3]),

            Stat::make('Cantidad Pedidos', $cantidadRecibidos)
                ->description('Cantidad recibidos')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3]),
        ];
    }


}
