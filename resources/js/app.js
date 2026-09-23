import './bootstrap';

import Alpine from 'alpinejs';
import Swal from 'sweetalert2';

window.Alpine = Alpine;
window.Swal = Swal;

Alpine.start();

/*
|--------------------------------------------------------------------------
| Configuración visual de SweetAlert2 para 101 Shop
|--------------------------------------------------------------------------
*/

const configuracionBase = {
    confirmButtonColor: '#2563eb',
    cancelButtonColor: '#64748b',
    reverseButtons: true,
    buttonsStyling: true,
};

/*
|--------------------------------------------------------------------------
| Mensajes enviados desde Laravel
|--------------------------------------------------------------------------
*/

const flashMessage = document.getElementById('flash-message');

if (flashMessage) {
    Swal.fire({
        ...configuracionBase,

        icon: flashMessage.dataset.type,

        title: flashMessage.dataset.title,

        text: flashMessage.dataset.message,

        confirmButtonText: 'Aceptar',

        cancelButtonText: undefined,

        showCancelButton: false,
    });
}

/*
|--------------------------------------------------------------------------
| Confirmaciones propias de 101 Shop
|--------------------------------------------------------------------------
|
| Cualquier formulario con la clase js-confirm-action utilizará
| SweetAlert2 en lugar del confirm() propio del navegador.
|
*/

document
    .querySelectorAll('.js-confirm-action')
    .forEach((form) => {

        form.addEventListener(
            'submit',
            async (event) => {

                event.preventDefault();

                const resultado = await Swal.fire({
                    ...configuracionBase,

                    icon:
                        form.dataset.icon
                        ?? 'warning',

                    title:
                        form.dataset.title
                        ?? 'Confirmar operación',

                    text:
                        form.dataset.message
                        ?? '¿Desea continuar?',

                    showCancelButton: true,

                    confirmButtonText:
                        form.dataset.confirmText
                        ?? 'Sí, continuar',

                    cancelButtonText:
                        'Cancelar',

                    focusCancel: true,
                });

                if (resultado.isConfirmed) {
                    form.submit();
                }
            }
        );
    });