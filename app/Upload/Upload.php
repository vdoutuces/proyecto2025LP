<?php

namespace App\Upload;

class Upload {
	public static function process($file, $destination, $allowedTypes = []) {
    	if (!isset($file) || $file[error] !== UPLOAD_ERR_OK) {
        	return [error => Error al subir el archivo.];
    	}

    	$fileName = $file[name];
    	$fileTmpPath = $file[tmp_name];
    	$fileSize = $file[size];
    	$fileType = $file[type];

    	$fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    	if (!empty($allowedTypes) && !in_array($fileExtension, $allowedTypes)) {
        	return [error => Tipo de archivo no permitido.];
    	}

    	$newFileName = uniqid() . . . $fileExtension;
    	$destinationPath = $destination . / . $newFileName;

    	if (move_uploaded_file($fileTmpPath, $destinationPath)) {
        	return [path => $destinationPath, name => $newFileName];
    	} else {
        	return [error => Error al mover el archivo al destino.];
    	}
	}
}

