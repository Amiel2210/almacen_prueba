<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreMovimientoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'producto_id' => ['required', 'exists:productos,id'],
            'almacen_id' => ['required', 'exists:almacenes,id'],
            'user_id' => ['required', 'exists:users,id'],
            'tipo_movimiento' => ['required', 'in:entrada,salida'],
        ];
    }

    public function messages(): array
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
}
