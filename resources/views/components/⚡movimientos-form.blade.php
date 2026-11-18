<?php

use App\Models\Almacen;
use App\Models\Movimiento;
use App\Models\Producto;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    public ?Movimiento $movimiento = null;
    public $producto_id = '';
    public $almacen_id = '';
    public $user_id = '';
    public $tipo_movimiento = '';

    protected function rules(): array
    {
        return [
            'producto_id' => ['required', 'exists:productos,id'],
            'almacen_id' => ['required', 'exists:almacenes,id'],
            'user_id' => ['required', 'exists:users,id'],
            'tipo_movimiento' => ['required', 'in:entrada,salida'],
        ];
    }

    protected function messages(): array
    {
        return [
            'producto_id.required' => 'Debe seleccionar un producto.',
            'producto_id.exists' => 'El producto seleccionado no es válido.',
            'almacen_id.required' => 'Debe seleccionar un almacén.',
            'almacen_id.exists' => 'El almacén seleccionado no es válido.',
            'user_id.required' => 'Debe seleccionar un usuario responsable.',
            'user_id.exists' => 'El usuario seleccionado no es válido.',
            'tipo_movimiento.required' => 'El tipo de movimiento es obligatorio.',
            'tipo_movimiento.in' => 'El tipo de movimiento debe ser entrada o salida.',
        ];
    }

    public function updated($propiedad)
    {
        $this->validateOnly($propiedad);
    }

    #[Computed]
    public function productos()
    {
        return Producto::orderBy('nombre')->get();
    }

    #[Computed]
    public function almacenes()
    {
        return Almacen::orderBy('nombre')->get();
    }

    #[Computed]
    public function usuarios()
    {
        return User::orderBy('name')->get();
    }

    public function guardar()
    {
        $datos =$this->validate();

        if ($this->movimiento) {
            $this->movimiento->update($datos);
        } else {
            Movimiento::create($datos);
        }

        $this->cancelar();
        session()->flash('ok', 'Movimiento guardado correctamente.');
        $this->dispatch('movimiento-guardado');
    }

    public function cancelar()
    {
        $this->reset();$this->resetValidation();
    }

    #[On('editar-movimiento')]
    public function editar($id)
    {
        $this->movimiento = Movimiento::findOrFail($id);$this->fill($this->movimiento->only('producto_id', 'almacen_id', 'user_id', 'tipo_movimiento'));$this->resetValidation();
    }
};
?>

<div class="max-w-7xl mx-auto p-4 mb-6 bg-white shadow-md rounded-lg">
    @if (session('ok'))
        <p class="text-green-700 font-semibold mb-4">{{ session('ok') }}</p>
    @endif

    <form wire:submit="guardar" class="space-y-4">
        <h2 class="text-lg font-bold">{{ $movimiento ? 'Editar movimiento' : 'Nuevo movimiento' }}</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="producto_id" class="block text-sm font-medium">Producto</label>
                <select id="producto_id" wire:model.blur="producto_id" class="w-full border rounded px-3 py-2 bg-white">
                    <option value="">Seleccione un producto...</option>
                    @foreach ($this->productos as$prod)
                        <option value="{{ $prod->id }}">{{ $prod->codigo }} - {{$prod->nombre }}</option>
                    @endforeach
                </select>
                @error('producto_id') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="almacen_id" class="block text-sm font-medium">Almacén</label>
                <select id="almacen_id" wire:model.blur="almacen_id" class="w-full border rounded px-3 py-2 bg-white">
                    <option value="">Seleccione un almacén...</option>
                    @foreach ($this->almacenes as$alm)
                        <option value="{{ $alm->id }}">{{ $alm->nombre }}</option>
                    @endforeach
                </select>
                @error('almacen_id') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="user_id" class="block text-sm font-medium">Usuario Responsable</label>
                <select id="user_id" wire:model.blur="user_id" class="w-full border rounded px-3 py-2 bg-white">
                    <option value="">Seleccione un usuario...</option>
                    @foreach ($this->usuarios as$usr)
                        <option value="{{ $usr->id }}">{{ $usr->name }}</option>
                    @endforeach
                </select>
                @error('user_id') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="tipo_movimiento" class="block text-sm font-medium">Tipo de Movimiento</label>
                <select id="tipo_movimiento" wire:model.blur="tipo_movimiento" class="w-full border rounded px-3 py-2 bg-white">
                    <option value="">Seleccione tipo...</option>
                    <option value="entrada">Entrada</option>
                    <option value="salida">Salida</option>
                </select>
                @error('tipo_movimiento') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Guardar</button>
            @if ($movimiento)
                <button type="button" wire:click="cancelar" class="px-4 py-2 bg-gray-400 text-white rounded">Cancelar</button>
            @endif
        </div>
    </form>
</div>
