<?php
header('Content-Type: application/json');

// Incluye el archivo de configuración de la base de datos
require_once '../app/config.php';

$response = [
    'success' => false,
    'message' => 'Error desconocido.'
];

// Verificación de la solicitud POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Método de solicitud no permitido.';
    echo json_encode($response);
    exit;
}

// Validación de la entrada
$videoId = isset($_POST['video_id']) ? (int)$_POST['video_id'] : 0;
// Aquí, en un sistema real, obtendrías el user_id del usuario logueado.
// Para este ejemplo, usaremos una dirección IP como un identificador básico.
$userId = $_SERVER['REMOTE_ADDR'];

if ($videoId <= 0) {
    $response['message'] = 'ID de video no válido.';
    echo json_encode($response);
    exit;
}

try {
    // 1. Verificar si el usuario ya ha votado por este video
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM votes WHERE video_id = ? AND user_id = ?");
    $stmt->execute([$videoId, $userId]);
    $votedCount = $stmt->fetchColumn();

    if ($votedCount > 0) {
        $response['message'] = 'Ya has votado por este video.';
        echo json_encode($response);
        exit;
    }

    // 2. Si el usuario no ha votado, registrar el voto en la tabla `votes`
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO votes (video_id, user_id) VALUES (?, ?)");
    $stmt->execute([$videoId, $userId]);

    // 3. Incrementar el contador de votos en la tabla `videos`
    $stmt = $pdo->prepare("UPDATE videos SET votes_count = votes_count + 1 WHERE id = ?");
    $stmt->execute([$videoId]);

    // 4. Obtener el nuevo total de votos para enviarlo de vuelta a JavaScript
    $stmt = $pdo->prepare("SELECT votes_count FROM videos WHERE id = ?");
    $stmt->execute([$videoId]);
    $newVotesCount = $stmt->fetchColumn();

    $pdo->commit();

    // Voto exitoso
    $response['success'] = true;
    $response['message'] = '¡Voto registrado con éxito!';
    $response['new_votes_count'] = $newVotesCount;
    
} catch (\PDOException $e) {
    $pdo->rollBack();
    // Error en la base de datos
    $response['message'] = 'Error en la base de datos. ' . $e->getMessage();
    // Aquí podrías loguear el error en el servidor para depuración.
    error_log("Error de PDO en vote.php: " . $e->getMessage());
}

echo json_encode($response);
?>