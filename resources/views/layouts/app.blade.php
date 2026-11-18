<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sistema de Almacén</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-[#FAFAFA] text-gray-800">
    <header class="bg-[#D81B60] text-white shadow-md mb-6">
        <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center font-bold">
            <span>Sistema de Almacén</span>
            <nav class="space-x-4 text-sm font-medium">
                <a href="/productos" class="hover:underline">Productos</a>
                <a href="/movimientos" class="hover:underline">Movimientos</a>
            </nav>
        </div>
    </header>

    <div class="container mx-auto px-4">
        {{ $slot }}
    </div>
</body>
</html>
