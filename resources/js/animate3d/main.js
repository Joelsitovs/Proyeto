import axios from "axios";
import * as THREE from "three";
import { OrbitControls } from "three/examples/jsm/controls/OrbitControls.js";
import { GLTFLoader } from "three/examples/jsm/loaders/GLTFLoader.js";

let intentos = 0;  // Contador de intentos
const maxIntentos = 5;  // Limitar a 5 intentos

async function getFirmadaUrl() {
    try {
            // esperar 2 segundos
            await new Promise(resolve => setTimeout(resolve, 2000));
        const response = await axios.get("/api/obtener-url-firmada");
        if (response.status === 200) {
            console.log("URL firmada obtenida:", response.data.urlFirmada);
            return response.data.urlFirmada;
        }else{
            console.error('Error al obtener la URL firmada1:', response);
        }
    }catch(error){
        console.error('Error al obtener la URL firmada2:', error);
        intentarReintentar();
    }
}
function intentarReintentar() {
    if (intentos < maxIntentos) {
        intentos++;
        console.log(`Reintentando obtener la URL firmada... Intento ${intentos} de ${maxIntentos}`);
        setTimeout(getFirmadaUrl, 1000);  // Reintentar después de 1 segundo
    } else {
        console.error("Se alcanzó el número máximo de intentos para obtener la URL firmada.");
    }
}
// Crear el renderizador y configurarlo
const renderer = new THREE.WebGLRenderer({ antialias: true });
const container = document.getElementById("threejs-container");
renderer.setSize(container.clientWidth, container.clientHeight);
container.appendChild(renderer.domElement); // Añadir el canvas al contenedor

// Crear la escena
const scene = new THREE.Scene();

// Crear la cámara
const camera = new THREE.PerspectiveCamera(
    45,
    container.clientWidth / container.clientHeight,
    0.1,
    1000
);
camera.position.set(0, 0, 3); // Acerca más la cámara

// Crear los controles de la cámara
const orbit = new OrbitControls(camera, renderer.domElement);
orbit.update();

// Añadir un grid a la escena (opcional)
const grid = new THREE.GridHelper(30, 30);
scene.add(grid);
console.log(
    `Posición del grid: X: ${grid.position.x.toFixed(
        2
    )} | Y: ${grid.position.y.toFixed(2)} | Z: ${grid.position.z.toFixed(2)}`
);

// Establecer color de fondo
renderer.setClearColor(0xa3a3a3); // Fondo gris claro

// Variables globales
let model = null;
let currentModel = null;

async function cargarNuevoModelo(url) {
    const signedUrl = await getFirmadaUrl();
    if (!signedUrl) {
        console.error("No se pudo obtener la URL firmada");
        return;
    }

    // Cargar el modelo GLTF
    const gltfLoader = new GLTFLoader();
    gltfLoader.load(
        signedUrl,
        function (gltf) {
            console.log("Modelo cargado exitosamente:", gltf);
            const model = gltf.scene;
            scene.add(model);

            // Escalar y posicionar el modelo
            model.scale.set(1, 1, 1); // O usa valores mayores si es necesario
            model.position.set(0, 0.5, 0); // Coloca el modelo en el origen
            model.rotation.set(0, 0, 0); // Asegúrate de que el modelo esté orientado correctamente

            // Calcular bounding box y centrar modelo
            const box = new THREE.Box3().setFromObject(model);
            const center = new THREE.Vector3();
            box.getCenter(center);
            model.position.sub(center); // Centrar el modelo

            // Ajustar escala para que encaje en la escena
            const size = new THREE.Vector3();
            box.getSize(size);
            const maxDimension = Math.max(size.x, size.y, size.z);
            const scaleFactor = 30 / maxDimension; // Escala relativa
            model.scale.set(scaleFactor, scaleFactor, scaleFactor);

            // Calcular la posición para centrar la caja
            const sceneCenter = new THREE.Vector3(0, 0, 0); // Este es el centro de la escena
            const sceneBox = new THREE.Box3().setFromObject(model);
            const sceneCenterPos = new THREE.Vector3();
            sceneBox.getCenter(sceneCenterPos); // Centro de la caja del modelo
            model.position.sub(sceneCenterPos).add(sceneCenter); // Mover el modelo para que esté centrado en la escena

            // Añadir el modelo a la escena
            scene.add(model);
        },
        undefined,
        function (error) {
            console.error("Error cargando el modelo:", error);
        }
    );
}

cargarNuevoModelo();
// Controlar la rotación con las teclas
function controlarRotacionModeloConTeclas() {
    document.addEventListener("keydown", function (event) {
        if (model) {
            switch (event.key) {
                case "ArrowUp":
                    model.position.x += 0.05; // Rotar hacia arriba en el eje X
                    break;
                case "ArrowDown":
                    model.rotation.x -= 0.05; // Rotar hacia abajo en el eje X
                    break;
                case "ArrowLeft":
                    model.rotation.y -= 0.05; // Rotar hacia la izquierda en el eje Y
                    break;
                case "ArrowRight":
                    model.rotation.y += 0.05; // Rotar hacia la derecha en el eje Y
                    break;
                case "w":
                    model.rotation.z += 0.05; // Rotar hacia adelante en el eje Z
                    break;
                case "s":
                    model.rotation.z -= 0.05; // Rotar hacia atrás en el eje Z
                    break;
            }

            // Mostrar la rotación en la consola
            console.log(
                `Rotación X: ${model.position.x.toFixed(
                    2
                )} | Rotación Y: ${model.rotation.y.toFixed(
                    2
                )} | Rotación Z: ${model.rotation.z.toFixed(2)}`
            );
        }
    });
}

// Controlar la rotación con los botones
function controlarRotacionModeloConBotones() {
    const rotateXPlus = document.getElementById("rotateXPlus");
    const rotateXMinus = document.getElementById("rotateXMinus");
    const rotateYPlus = document.getElementById("rotateYPlus");
    const rotateYMinus = document.getElementById("rotateYMinus");
    const rotateZPlus = document.getElementById("rotateZPlus");
    const rotateZMinus = document.getElementById("rotateZMinus");

    if (rotateXPlus) {
        rotateXPlus.addEventListener("click", function () {
            if (model) model.rotation.x += 0.05;
            console.log(
                `Rotación X: ${model.rotation.x.toFixed(
                    2
                )} | Rotación Y: ${model.rotation.y.toFixed(
                    2
                )} | Rotación Z: ${model.rotation.z.toFixed(2)}`
            );
        });
    }

    if (rotateXMinus) {
        rotateXMinus.addEventListener("click", function () {
            if (model) model.rotation.x -= 0.05;
            console.log(
                `Rotación X: ${model.rotation.x.toFixed(
                    2
                )} | Rotación Y: ${model.rotation.y.toFixed(
                    2
                )} | Rotación Z: ${model.rotation.z.toFixed(2)}`
            );
        });
    }

    if (rotateYPlus) {
        rotateYPlus.addEventListener("click", function () {
            if (model) model.rotation.y += 0.05;
            console.log(
                `Rotación X: ${model.rotation.x.toFixed(
                    2
                )} | Rotación Y: ${model.rotation.y.toFixed(
                    2
                )} | Rotación Z: ${model.rotation.z.toFixed(2)}`
            );
        });
    }

    if (rotateYMinus) {
        rotateYMinus.addEventListener("click", function () {
            if (model) model.rotation.y -= 0.05;
            console.log(
                `Rotación X: ${model.rotation.x.toFixed(
                    2
                )} | Rotación Y: ${model.rotation.y.toFixed(
                    2
                )} | Rotación Z: ${model.rotation.z.toFixed(2)}`
            );
        });
    }

    if (rotateZPlus) {
        rotateZPlus.addEventListener("click", function () {
            if (model) model.rotation.z += 0.05;
            console.log(
                `Rotación X: ${model.rotation.x.toFixed(
                    2
                )} | Rotación Y: ${model.rotation.y.toFixed(
                    2
                )} | Rotación Z: ${model.rotation.z.toFixed(2)}`
            );
        });
    }

    if (rotateZMinus) {
        rotateZMinus.addEventListener("click", function () {
            if (model) model.rotation.z -= 0.05;
            console.log(
                `Rotación X: ${model.rotation.x.toFixed(
                    2
                )} | Rotación Y: ${model.rotation.y.toFixed(
                    2
                )} | Rotación Z: ${model.rotation.z.toFixed(2)}`
            );
        });
    }
}

// Añadir luces a la escena
const ambientLight = new THREE.AmbientLight(0x404040, 1); // Luz suave
scene.add(ambientLight);

const directionalLight = new THREE.DirectionalLight(0xffffff, 1);
directionalLight.position.set(5, 5, 5);
scene.add(directionalLight);

// Bucle de animación
function animate(time) {
    renderer.render(scene, camera);
    requestAnimationFrame(animate);
}

animate();

// Redimensionar la ventana
window.addEventListener("resize", function () {
    camera.aspect = container.clientWidth / container.clientHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(container.clientWidth, container.clientHeight);
});

// Iniciar controles de rotación
controlarRotacionModeloConTeclas();
controlarRotacionModeloConBotones();
