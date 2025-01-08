<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Aws\S3\S3Client;
use GuzzleHttp\Client;
use App\Models\FileUploadUser;

class SubidaS3Controller extends Controller
{

    private $bucketName;
    private $region;
    private $accessKey;
    private $secretKey;

    private $clientId;

    private $clientSecret;

    public function __construct()
    {
        $this->bucketName = env('VITE_AWS_BUCKET');
        $this->region = env('VITE_AWS_DEFAULT_REGION');
        $this->accessKey = env('VITE_AWS_ACCESS_KEY_ID');
        $this->secretKey = env('VITE_AWS_SECRET_ACCESS_KEY');
        $this->clientId = env('ASPOSE_CLIENT_ID');
        $this->clientSecret = env('ASPOSE_CLIENT_SECRET');
    }

    public function obtenerCredenciales($clientId, $clientSecret)
    {
        try {
            $client = new Client();
            $url = 'https://api.aspose.cloud/connect/token';
            $response = $client->post($url, [
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (!isset($data['access_token'])) {
                throw new \Exception('No se pudo obtener el token de acceso');
            }

            return $data['access_token'];
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }



    public function upload(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file',
            ]);

            $file = $request->file('file');
            $uuid = Str::uuid()->toString();

            if (!$file->isValid()) {
                throw new \Exception('El archivo no es válido');
            }
            if (Auth::check()) {
                $usuario = Auth::user();
                $nombreArchivo = "{$usuario->id}_" . time() . ".stl";
                $newFileName = "{$usuario->id}_" . time() . ".gltf";
            } else {
                $nombreArchivo = "{$uuid}."."stl";
                $newFileName = "{$uuid}.gltf";
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }

        $folderName = Auth::check() ? 'usuarios/' . Auth::user()->id . '/' . $uuid . '/' : 'no-registrados/' . $uuid . '/';
        $key = $folderName . $nombreArchivo;
        try {

            $s3Client = new s3Client([
                'version' => 'latest',
                'region' => $this->region,
                'credentials' => [
                    'key' => $this->accessKey,
                    'secret' => $this->secretKey,
                ],
            ]);

            //Subir el archivo al Bucket
            $result = $s3Client->putObject([
                'Bucket' => $this->bucketName,
                'Key' => $key,
                'Body' => fopen($file->getRealPath(), 'r'),
                'ACL' => 'public-read',
            ]);


            return response()->json([
                'status' => 'success',
                'message' => 'Archivo subido correctamente',
                'newFileName' => $newFileName,
                'nombreArchivo' => $nombreArchivo,
                'folderName' => $folderName,

            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    public function StlToGLFT($newFileName, $nombreArchivo, $folderName)
    {
        $StorageName = 'morfeo3d';

        $access_token = $this->obtenerCredenciales($this->clientId, $this->clientSecret);

        $client = new Client(); // Define the $client variable

        $url = 'https://api.aspose.cloud/v3.0/3d/saveas/newformat';

        $converResponse = $client->post($url, [
            'headers' => [
                'authorization' => 'Bearer ' . $access_token,
                'Accept' => 'application/json',
            ],
            'query' => [
                'name' => $nombreArchivo,
                'newformat' => 'gltf2_binary',
                'newfilename' => $newFileName,
                'folder' => $folderName,
                'IsOverwrite' => 'false',
                'storage' => $StorageName,
            ],
        ]);
        if ($converResponse->getStatusCode() !== 200) {
            throw new \Exception('No se pudo convertir el archivo');
        }
        return response()->json(['status' => 'success', 'message' => 'Archivo convertido correctamente']);
    }

    public function ejecutarSubidaYConversion(Request $request)
    {
        try {
            $startTime = microtime(true);

            $uploadResult = $this->upload($request);
            $uploadResultData = json_decode($uploadResult->getContent(), true);

            if ($uploadResultData['status'] !== 'success') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'La subida del archivo falló.',
                ]);
            }

            $newFileName = $uploadResultData['newFileName'];
            $nombreArchivo = $uploadResultData['nombreArchivo'];
            $folderName = $uploadResultData['folderName'];

            try {
                $this->StlToGLFT($newFileName, $nombreArchivo, $folderName);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'La conversión del archivo falló: ' . $e->getMessage(),
                ]);
            }



            FileUploadUser::create([
                'folder_name' => $folderName, 
                'new_file_name' => $newFileName, 
                'file_name' => $nombreArchivo, 
                'url_file_stl' => trim("{$folderName}{$nombreArchivo}"), 
                'url_file_gltf' => trim("{$folderName}{$newFileName}"),
                'url_firmada' => null, 
                'id_user' => Auth::check() ? Auth::user()->id : null, 
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Archivo subido y convertido correctamente',

            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    
public function obtenerDatos()
{
    $archivos = FileUploadUser::where('id_user', Auth::user()->id)->latest()->get();

    if($archivos->isEmpty()) {
        return response()->json([
            'status' => 'error',
            'message' => 'No se encontraron archivos',
        ]);
    }


    return response()->json([
        'folderName' => $archivos[0]->folder_name,
        'fileName' => $archivos[0]->new_file_name,
    ]);

}


public function guardarUrlFirmada(Request $request)
{
    $urlFirmada = $request->input('urlFirmada');

    // Validación de que la URL no esté vacía
    if (!$urlFirmada) {
        return response()->json([
            'status' => 'error',
            'message' => 'La URL firmada es nula o vacía',
        ], 400); // Código de estado 400 Bad Request
    }

    // Obtener el registro existente (asumiendo que se relaciona con el usuario autenticado)
    $archivo = FileUploadUser::where('id_user', Auth::user()->id)->latest()->first();

    if (!$archivo) {
        return response()->json([
            'status' => 'error',
            'message' => 'No se encontró el archivo para este usuario',
        ], 404); // Código de estado 404 Not Found
    }

    try {
        // Actualizar el registro existente
        $archivo->url_firmada = $urlFirmada; // Asignar el valor a la propiedad del modelo
        $archivo->save(); // Guardar los cambios en la base de datos

        return response()->json([
            'status' => 'success',
            'message' => 'URL firmada guardada correctamente',
        ]);
    } catch (\Exception $e) {
      
        return response()->json([
            'status' => 'error',
            'message' => 'Error al guardar la URL: ' . $e->getMessage(),
        ], 500); // Código de estado 500 Internal Server Error
    }
}

    public function obtenerUrlFirmada(){
        $archivo = FileUploadUser::where('id_user', Auth::user()->id)->latest()->first();

        if(!$archivo) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se encontró el archivo',
            ]);
        }

        if(!$archivo->url_firmada) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se encontró la URL firmada',
            ]);
        }

        return response()->json([
            'status' => 'success',
            'urlFirmada' => $archivo->url_firmada,
        ]);
    }


}
