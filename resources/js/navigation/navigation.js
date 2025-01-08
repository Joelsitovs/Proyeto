
function handleResize() {
    const responsiveDiv2 = document.getElementById('responsiveDiv2');
    if (window.innerWidth < 640) {
        responsiveDiv2.classList.remove('rounded-3xl');
    } else {
       responsiveDiv2.classList.add('rounded-3xl');
    }

    const responsiveDiv = document.getElementById('responsiveDiv');
    if (window.innerWidth < 640) {
        responsiveDiv.classList.remove('mb-10');
        responsiveDiv.classList.remove('mt-10');
    } else {
        responsiveDiv.classList.add('mb-10');
        responsiveDiv.classList.add('mt-10');
    }
}

// Ejecutar al cargar y cuando se redimensiona la ventana
window.addEventListener('resize', handleResize);
handleResize();

