<?php

use App\Models\Almacen;
use App\Models\Movimiento;
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
    public $tipo_movimiento = '';

    public function updatedBuscar()
    {
        $this->resetPage();
    }

    public function updatedAlmacenId()
    {
        $this->resetPage();
    }

    public function updatedTipoMovimiento()
    {
        $this->resetPage();
    }

    #[On('movimiento-guardado')]
    public function refrescar()
    {
    }

    #[Computed]
    public function almacenes()
    {
        return Almacen::orderBy('nombre')->get();
    }

    #[Computed]
    public function movimientos()
    {
        $consulta = Movimiento::with(['producto', 'almacen', 'user']);

        if ($this->buscar) {
            $consulta->whereHas('producto', function ($q) {
                $q->where('nombre', 'like', '%' . $this->buscar . '%')
                  ->orWhere('codigo', 'like', '%' . $this->buscar . '%');
            });
        }

        if ($this->almacen_id) {
            $consulta->where('almacen_id', $this->almacen_id);
        }

        if ($this->tipo_movimiento) {
            $consulta->where('tipo_movimiento', $this->tipo_movimiento);
        }

        return $consulta->latest()->paginate(10);
    }
};
?>

<div class="max-w-7xl mx-auto p-4">
    <!-- Barra de Búsqueda y Filtros -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <input
            type="text"
            wire:model.live.debounce.400ms="buscar"
            placeholder="Buscar por producto o código..."
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
            wire:model.live="tipo_movimiento"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
            <option value="">Todos los tipos</option>
            <option value="entrada">Entradas</option>
            <option value="salida">Salidas</option>
        </select>
    </div>

    <!-- Tabla de datos -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-100 text-gray-700 font-bold uppercase">
                <tr>
                    <th class="px-6 py-3">Producto</th>
                    <th class="px-6 py-3">Almacén</th>
                    <th class="px-6 py-3">Tipo</th>
                    <th class="px-6 py-3">Responsable</th>
                    <th class="px-6 py-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($this->movimientos as $movimiento)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4">{{ $movimiento->producto->nombre ?? 'Sin producto' }}</td>
                        <td class="px-6 py-4">{{ $movimiento->almacen->nombre ?? 'Sin almacén' }}</td>
                        <td class="px-6 py-4 uppercase font-semibold">{{ $movimiento->tipo_movimiento }}</td>
                        <td class="px-6 py-4">{{ $movimiento->user->name ?? 'Sin usuario' }}</td>
                        <td class="px-6 py-4">
                            <button
                                type="button"
                                wire:click="$dispatch('editar-movimiento', { id: {{ $movimiento->id }} })"
                                class="text-blue-600 hover:underline font-medium"
                            >
                                Editar
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No hay movimientos registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="mt-4">
        {{ $this->movimientos->links() }}
    </div>
</div>
