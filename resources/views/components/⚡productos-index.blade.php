<?php

use App\Models\Almacen;
use App\Models\Producto;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    #[Url]
    public $buscar = '';

    #[Url]
    public $almacen_id = '';

    #[Url]
    public $estado_stock = '';

    #[Url]
    public $rango_precio = '';

    public function updatedBuscar()
    {
        $this->resetPage();
    }

    public function updatedAlmacenId()
    {
        $this->resetPage();
    }

    public function updatedEstadoStock()
    {
        $this->resetPage();
    }

    public function updatedRangoPrecio()
    {
        $this->resetPage();
    }

    #[On('producto-guardado')]
    public function refrescar()
    {
    }

    #[Computed]
    public function almacenes()
    {
        return Almacen::orderBy('nombre')->get();
    }

    #[Computed]
    public function productos()
    {
        $consulta = Producto::with(['stock_almacen']);

        if ($this->buscar) {
            $consulta->where(function ($q) {
                $q->where('nombre', 'like', '%' . $this->buscar . '%')
                  ->orWhere('codigo', 'like', '%' . $this->buscar . '%');
            });
        }

        if ($this->almacen_id) {
            $consulta->join('stock_almacen', 'productos.id', '=', 'stock_almacen.producto_id')
                     ->where('stock_almacen.almacen_id', $this->almacen_id)
                     ->select('productos.*');
        }

        if ($this->estado_stock === 'con_stock') {
            $consulta->whereHas('stock_almacen', function ($q) {
                $q->where('stock', '>', 0);
            });
        } elseif ($this->estado_stock === 'sin_stock') {
            $consulta->whereDoesntHave('stock_almacen', function ($q) {
                $q->where('stock', '>', 0);
            });
        }

        if ($this->rango_precio === 'bajo') {
            $consulta->where('precio', '<', 50);
        } elseif ($this->rango_precio === 'medio') {
            $consulta->whereBetween('precio', [50, 200]);
        } elseif ($this->rango_precio === 'alto') {
            $consulta->where('precio', '>', 200);
        }

        return $consulta->orderBy('productos.nombre')->paginate(10);
    }
};
?>

<div class="max-w-7xl mx-auto p-4">
    <!-- Barra de Búsqueda y Filtros -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
        <input
            type="text"
            wire:model.live.debounce.400ms="buscar"
            placeholder="Buscar por nombre o código..."
            class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
        >

        <select
            wire:model.live="almacen_id"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
            <option value="">Todos los almacenes</option>
            @foreach ($this->almacenes as $almacen)
                <option value="{{ $almacen->id }}">{{ $almacen->nombre }}</option>
            @endforeach
        </select>

        <select
            wire:model.live="estado_stock"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
            <option value="">Todo el stock</option>
            <option value="con_stock">Con stock disponible</option>
            <option value="sin_stock">Sin stock (Agotados)</option>
        </select>

        <select
            wire:model.live="rango_precio"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
            <option value="">Todos los precios</option>
            <option value="bajo">Económicos (Menor a S/ 50)</option>
            <option value="medio">Intermedios (S/ 50 - S/ 200)</option>
            <option value="alto">Costosos (Mayor a S/ 200)</option>
        </select>
    </div>

    <!-- Tabla de datos -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-100 text-gray-700 font-bold uppercase">
                <tr>
                    <th class="px-6 py-3">Código</th>
                    <th class="px-6 py-3">Nombre</th>
                    <th class="px-6 py-3">Descripción</th>
                    <th class="px-6 py-3">Precio</th>
                    <th class="px-6 py-3">Stock</th>
                    <th class="px-6 py-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($this->productos as $producto)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4">{{ $producto->codigo }}</td>
                        <td class="px-6 py-4">{{ $producto->nombre }}</td>
                        <td class="px-6 py-4">{{ $producto->descripcion ?? '-' }}</td>
                        <td class="px-6 py-4">S/ {{ number_format($producto->precio, 2) }}</td>
                        <td class="px-6 py-4">{{ $producto->stock_almacen->sum('stock') }}</td>
                        <td class="px-6 py-4">
                            <button
                                type="button"
                                wire:click="$dispatch('editar-producto', { id: {{ $producto->id }} })"
                                class="text-blue-600 hover:underline font-medium"
                            >
                                Editar
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No hay productos que coincidan con los filtros.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="mt-4">
        {{ $this->productos->links() }}
    </div>
</div>
