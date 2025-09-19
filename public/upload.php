<?php
// Incluye el archivo de configuración de la base de datos
require_once '../app/config.php';

// Verifica si la solicitud es un envío de formulario (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Validar los datos del formulario
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';

    if (empty($title) || empty($description)) {
        echo "<script>alert('Error: El título y la descripción son campos obligatorios.'); window.history.back();</script>";
        exit;
    }

    // 2. Manejar la subida del archivo
    if (isset($_FILES['video-file']) && $_FILES['video-file']['error'] === UPLOAD_ERR_OK) {
        $videoFile = $_FILES['video-file'];

        // Validación del tamaño del archivo (1024 MB = 1024 * 1024 * 1024 bytes)
        $maxFileSize = 1073741824; // 1024 MB
        if ($videoFile['size'] > $maxFileSize) {
            echo "<script>alert('Error: El video es demasiado grande. El tamaño máximo es 1024 MB.'); window.history.back();</script>";
            exit;
        }

        // Validación del tipo de archivo
        $allowedMimeTypes = ['video/mp4', 'video/webm', 'video/ogg'];
        if (!in_array($videoFile['type'], $allowedMimeTypes)) {
            echo "<script>alert('Error: Tipo de archivo no permitido. Solo se aceptan MP4, WebM y Ogg.'); window.history.back();</script>";
            exit;
        }

        // Generar un nombre de archivo único
        $fileExtension = pathinfo($videoFile['name'], PATHINFO_EXTENSION);
        $fileName = uniqid() . '.' . $fileExtension;
        $destinationPath = __DIR__ . '/videos/' . $fileName;

        // 3. Mover el archivo subido a la carpeta de destino
        if (move_uploaded_file($videoFile['tmp_name'], $destinationPath)) {
            // El archivo se subió con éxito, ahora inserta la información en la base de datos

            // 4. Insertar en la base de datos (con sentencias preparadas)
            try {
                $stmt = $pdo->prepare("INSERT INTO videos (title, description, file_path, upload_date) VALUES (?, ?, ?, NOW())");
                $stmt->execute([$title, $description, 'videos/' . $fileName]);

                // Proceso completado con éxito
                echo "<script>alert('¡Video subido con éxito!'); window.location.href = 'index.html';</script>";
                exit;

            } catch (\PDOException $e) {
                // Si la inserción falla, elimina el archivo para evitar archivos "huérfanos"
                unlink($destinationPath);
                echo "<script>alert('Error en la base de datos: " . $e->getMessage() . "'); window.history.back();</script>";
                // Aquí podrías loguear el error en el servidor para depuración
                error_log("Error en la base de datos: " . $e->getMessage() . " en el archivo " . __FILE__ . " en la línea " . __LINE__);
                exit;
            }

        } else {
            // Error al mover el archivo
            echo "<script>alert('Error: Ocurrió un error al subir el video.'); window.history.back();</script>";
            exit;
        }

    } else {
        // Error de subida o no se seleccionó archivo
        echo "<script>alert('Error: Debes seleccionar un archivo de video. Código de error: " . $_FILES['video-file']['error'] . "'); window.history.back();</script>";
        exit;
    }
}
?>