document.addEventListener('DOMContentLoaded', () => {
  const slides = document.querySelectorAll('.slide');
  const prev = document.querySelector('.prev');
  const next = document.querySelector('.next');

  let currentSlide = 0;

  // Función para mostrar una diapositiva específica
  function showSlide(n) {
    slides.forEach((slide) => {
      slide.style.display = 'none'; // Asegúrate de que todas las diapositivas estén ocultas
    });
    // Verifica que el índice esté dentro de los límites
    if (n >= 0 && n < slides.length) {
      slides[n].style.display = 'block';
    }
  }
  
  // Mostrar la primera diapositiva al cargar la página
  showSlide(currentSlide);
  
  // Evento para el botón "Siguiente"
  next.addEventListener('click', () => {
    currentSlide = (currentSlide + 1) % slides.length; // Ciclo circular
    showSlide(currentSlide);
  });
  
  // Evento para el botón "Anterior"
  prev.addEventListener('click', () => {
    currentSlide = (currentSlide - 1 + slides.length) % slides.length; // Ciclo circular
    showSlide(currentSlide);
  });

  function autoplay() {
    currentSlide = (currentSlide + 1) % slides.length;
    showSlide(currentSlide);
  }
  
  // Iniciar la reproducción automática cada 3 segundos (ajusta el tiempo según tus necesidades)
  setInterval(autoplay, 4000);
});
