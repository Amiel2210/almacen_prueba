<?php

use App\Models\Almacen;
use App\Models\Categoria;
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
    public $categoria_id = '';

    #[Url]
    public $almacen_id = '';

    #[Url]
    public $rango_precio = '';

    public function updatedBuscar()
    {
        $this->resetPage();
    }

    public function updatedCategoriaId()
    {
        $this->resetPage();
    }

    public function updatedAlmacenId()
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
    public function categorias()
    {
        return Categoria::orderBy('nombre')->get();
    }

    #[Computed]
    public function almacenes()
    {
        return Almacen::orderBy('nombre')->get();
    }

    #[Computed]
    public function productos()
    {
        $consulta = Producto::with(['categoria', 'stock_almacen']);

        if ($this->buscar) {
            $consulta->where(function ($q) {
                $q->where('nombre', 'like', '%' . $this->buscar . '%')
                  ->orWhere('codigo', 'like', '%' . $this->buscar . '%');
            });
        }

        if ($this->categoria_id) {
            $consulta->where('categoria_id', $this->categoria_id);
        }

        if ($this->almacen_id) {
            $consulta->join('stock_almacen', 'productos.id', '=', 'stock_almacen.producto_id')
                     ->where('stock_almacen.almacen_id', $this->almacen_id)
                     ->select('productos.*');
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
            wire:model.live="categoria_id"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
            <option value="">Todas las categorías</option>
            @foreach ($this->categorias as $categoria)
                <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
            @endforeach
        </select>

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
                    <th class="px-6 py-3">Categoría</th>
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
                        <td class="px-6 py-4">{{ $producto->categoria->nombre ?? 'Sin categoría' }}</td>
                        <td class="px-6 py-4">S/ {{ number_format($producto->precio, 2) }}</td>
                        <td class="px-6 py-4">{{ $producto->stock_almacen->first()?->stock ?? 0 }}</td>
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
