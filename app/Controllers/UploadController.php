<?php
namespace App\Controllers;

use App\Upload\Upload;

class UploadController {
    public function index() {
        view("upload_form");
    }

    public function upload() {
        $uploadResult = Upload::process($_FILES["archivo"], "public/uploads", ["jpg", "png", "gif"]);

        if (isset($uploadResult["error"])) {
            echo $uploadResult["error"];
        } else {
            echo "Archivo subido correctamente: " . $uploadResult["path"];
        }
    }
}

