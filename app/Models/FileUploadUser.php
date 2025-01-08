<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileUploadUser extends Model
{
    use HasFactory;

    protected $table = 'file_upload_users'; // Nombre de la tabla (en plural y snake_case)

    protected $fillable = [
        'folder_name', // snake_case
        'new_file_name', // snake_case
        'file_name', // snake_case
        'url_file_stl', // snake_case
        'url_file_gltf', // snake_case
        'url_firmada', // snake_case
        'id_user',
    ];

    /**
     * Relación con el modelo User.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}