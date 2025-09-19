document.addEventListener('DOMContentLoaded', () => {

    const videoGrid = document.querySelector('.video-grid');

    if (videoGrid) {
        videoGrid.addEventListener('click', async (event) => {
            // Verifica si el clic fue en un botón de votar
            if (event.target.classList.contains('vote-button')) {
                const voteButton = event.target;
                const videoCard = voteButton.closest('.video-card');
                const videoId = voteButton.dataset.videoId;

                // Deshabilita el botón para evitar múltiples clics
                voteButton.disabled = true;
                voteButton.textContent = 'Votando...';

                // Muestra un mensaje de alerta si el videoId no está disponible
                if (!videoId) {
                    alert('Error: No se pudo obtener el ID del video. Por favor, recarga la página.');
                    voteButton.disabled = false;
                    voteButton.textContent = 'Votar';
                    return;
                }

                try {
                    // Envía la solicitud AJAX al servidor
                    const response = await fetch('vote.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `video_id=${videoId}`
                    });

                    // Procesa la respuesta del servidor
                    const result = await response.json();

                    if (result.success) {
                        // Actualiza el contador de votos en el HTML
                        const votesCountElement = videoCard.querySelector('.votes');
                        votesCountElement.textContent = `${result.new_votes_count} votos`;

                        // Muestra una alerta de éxito
                        alert(result.message);
                    } else {
                        // Muestra una alerta de error
                        alert(result.message);
                    }

                } catch (error) {
                    console.error('Error en la solicitud:', error);
                    alert('Error: No se pudo conectar con el servidor.');
                } finally {
                    // Vuelve a habilitar el botón y su texto
                    voteButton.disabled = false;
                    voteButton.textContent = 'Votar';
                }
            }
        });
    }

});