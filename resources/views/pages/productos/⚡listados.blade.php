<?php

use Livewire\Component;
use App\Models\Producto;
use Livewire\Attributes\Computed;


new class extends Component
{
};
?>

@extends('layouts.app')
@section('contenido')

<div>
    @livewire('productos-form')
    @livewire('productos-index')
</div>

@endsection
