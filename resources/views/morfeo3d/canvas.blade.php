<x-app-layout>
    <!-- Contenedor para el canvas -->
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">
        <div id="threejs-container" class="bg-black w-full h-96">
            <!-- El canvas se añadirá aquí por JavaScript -->
        </div>
    </div>
<!-- Slider de rotación -->
<input type="range" id="slider-rotacion-x" min="-180" max="180" step="1" value="0">
<input type="range" id="slider-rotacion-y" min="-180" max="180" step="1" value="0">
<input type="range" id="slider-rotacion-z" min="-180" max="180" step="1" value="0">
<button id="rotateXPlus">Rotar X +</button>
<button id="rotateXMinus">Rotar X -</button>
<button id="rotateYPlus">Rotar Y +</button>
<button id="rotateYMinus">Rotar Y -</button>
<button id="rotateZPlus">Rotar Z +</button>
<button id="rotateZMinus">Rotar Z -</button>

    <!-- Vite carga tu archivo JavaScript -->
    @vite('resources/js/animate3d/firmar.js')
    @vite('resources/js/animate3d/main.js')
  
</x-app-layout>
