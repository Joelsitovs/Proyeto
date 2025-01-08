// Primero, define la función de validación
function validateFile(file) {
    const maxSize = 100 * 1024 * 1024; // 20MB
    const allowedExtensions = ['stl', 'STL'];

    // Verificar el tamaño del archivo
    if (file.size > maxSize) {
        alert('El archivo es muy grande, el tamaño máximo permitido es de 20MB');
        return false;
    }

    // Verificar la extensión del archivo
    const fileExtension = file.name.split('.').pop();
    if (!allowedExtensions.includes(fileExtension)) {
        alert('El archivo seleccionado no es un archivo STL');
        return false;
    }

    return true;
}

// Luego, añade el evento 'submit' para el formulario
document.getElementById('upload-form').addEventListener('submit', function(event) {
    event.preventDefault();  // Evitar el envío tradicional del formulario

    const fileInput = document.getElementById('file-input');
    const file = fileInput.files[0]; // Obtener el archivo seleccionado

    // Validar el archivo
    if (!validateFile(file)) {
        return; // Detener la ejecución si la validación falla
    }

    // Si la validación pasa, proceder con el envío del archivo
    const formData = new FormData();
    formData.append('file', file);

    // Agregar el token CSRF al formulario
    formData.append('_token', document.querySelector('input[name="_token"]').value);

    let serverResponse = null;
    // Hacer la solicitud AJAX para subir el archivo
    fetch('/upload_s3', {
        method: 'POST',
        body: formData,
        headers: {
        }
    })
    .then(response => response.json())
    .then(data => {
        serverResponse = data;
        if (data.status === 'success') {
            console.log('Archivo subido correctamente');
            //mostar datos 
            console.log(data);    
            window.location.href = '/canvas';
        } else {
            console.error('Error en la carga del archivo:', data.message);
        }
        console.log(serverResponse);
    })
    
    
    .catch(error => {
        console.error('Error en la solicitud:', error);
    });
});
