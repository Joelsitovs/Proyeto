import { S3Client, GetObjectCommand } from '@aws-sdk/client-s3';
import { getSignedUrl } from '@aws-sdk/s3-request-presigner';


async function obtenerDatosDesdeBaseDeDatos() {
    try {
        const response = await fetch('/api/obtener-datos-base');
        if (response.ok) {
            const data = await response.json();
            console.log('Datos obtenidos:', data);

            const folderName = data.folderName;
            const fileName = data.fileName;

        
            console.log("folderName (antes de path.join):", folderName);
            console.log("fileName (antes de path.join):", fileName);

            const gltfKey = `${folderName}${fileName}`;

            console.log("gltfkey: ", gltfKey);

            configurarClienteS3(gltfKey); 
        }
    } catch (error) {
        console.error('Error al obtener los datos:', error);
    }
}

function configurarClienteS3(gltfKey) { 
    const s3Client = new S3Client({
        region: import.meta.env.VITE_AWS_DEFAULT_REGION,
        credentials: {
            accessKeyId: import.meta.env.VITE_AWS_ACCESS_KEY_ID,
            secretAccessKey: import.meta.env.VITE_AWS_SECRET_ACCESS_KEY,
        },
    });

    const gltfBucket = import.meta.env.VITE_AWS_BUCKET;
    const gltfExpiracion = 60 * 10;

    generarUrlFirmada(s3Client, gltfBucket, gltfKey, gltfExpiracion);
}

async function generarUrlFirmada(s3Client, bucket, key, expiracion) {
    const params = {
        Bucket: bucket,
        Key: key,
    };

    try {
        const url = await getSignedUrl(s3Client, new GetObjectCommand(params), { expiresIn: expiracion });
        console.log('URL firmada GLTF:', url);

    
        try {
            const testResponse = await fetch(url);
            if (testResponse.ok) {
                console.log("Prueba de URL firmada: ¡Funciona!");
            } else {
                console.error("Prueba de URL firmada: ¡Error!", testResponse.status, testResponse.statusText);
            }
        } catch (testError) {
            console.error("Error al probar la URL firmada:", testError);
        }

        await guardarUrlFirmadaEnBaseDeDatos(url);
    } catch (error) {
        console.error('Error al generar la URL firmada:', error);
        console.error("Detalles del error:", error.message, error.stack); 
    }
}

function guardarUrlFirmadaEnBaseDeDatos(url) {
  axios.post('/api/guardar-url-firmada', { urlFirmada: url }, {
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
    }
  })
  .then(response => {
      console.log('URL firmada guardada correctamente', response);
  })
  .catch(error => {
      if (error.response) {
 
          console.error('Error en la respuesta del servidor:', error.response.data);
      } else if (error.request) {
   
          console.error('Error en la solicitud:', error.request);
      } else {
        
          console.error('Error desconocido:', error.message);
      }
  });
}

// Llamada inicial para obtener datos desde la base de datos
obtenerDatosDesdeBaseDeDatos();
