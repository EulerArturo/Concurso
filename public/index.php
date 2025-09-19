<?php
// Incluye el archivo de configuración de la base de datos
require_once '../app/config.php';

// Definición de una función para obtener todos los videos
function getVideos($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM videos ORDER BY upload_date DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
        // En caso de error, puedes manejarlo o simplemente devolver un array vacío
        error_log("Error al obtener videos: " . $e->getMessage());
        return [];
    }
}

// Obtener la lista de videos de la base de datos
$videos = getVideos($pdo);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concurso de Videos - ¡Vota por tu favorito!</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <header>
        <h1>Concurso de Videos</h1>
        <nav>
            <ul>
                <li><a href="#videos">Ver Videos</a></li>
                <li><a href="#subir">Subir Video</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section id="videos">
            <h2>Videos Participantes</h2>
            <div class="video-grid">
                <?php if (count($videos) > 0): ?>
                    <?php foreach ($videos as $video): ?>
                        <article class="video-card">
                            <video controls src="<?php echo htmlspecialchars($video['file_path']); ?>" poster="images/default_thumbnail.jpg"></video>
                            <h3><?php echo htmlspecialchars($video['title']); ?></h3>
                            <p class="description"><?php echo htmlspecialchars($video['description']); ?></p>
                            <div class="meta-data">
                                <span class="votes"><?php echo htmlspecialchars($video['votes_count']); ?> votos</span>
                                <button class="vote-button" data-video-id="<?php echo htmlspecialchars($video['id']); ?>">Votar</button>
                            </div>
                            <div class="comments-section">
                                <h4>Comentarios</h4>
                                <ul class="comments-list">
                                    </ul>
                                <form class="comment-form">
                                    <input type="text" placeholder="Añadir un comentario..." required>
                                    <button type="submit">Enviar</button>
                                </form>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-videos-message">No hay videos subidos aún. ¡Sé el primero en participar!</p>
                <?php endif; ?>
            </div>
        </section>

        <section id="subir">
            <h2>Sube tu Video</h2>
            <form action="upload.php" method="POST" enctype="multipart/form-data">
                <label for="video-file">Seleccionar Video (máx. 1024MB):</label>
                <input type="file" id="video-file" name="video-file" accept="video/*" required>
                
                <label for="title">Título del Video:</label>
                <input type="text" id="title" name="title" required>
                
                <label for="description">Descripción:</label>
                <textarea id="description" name="description" rows="4" required></textarea>
                
                <button type="submit">Subir Video</button>
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; 2023 Concurso de Videos. Todos los derechos reservados.</p>
    </footer>
    
    <script src="js/script.js"></script>
</body>
</html>