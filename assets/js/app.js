document.addEventListener("DOMContentLoaded", () => {
    const botonMenu = document.querySelector(".boton-menu");
    const menu = document.querySelector(".menu-principal");

    if (botonMenu && menu) {
        botonMenu.addEventListener("click", () => {
            const abierto = botonMenu.getAttribute("aria-expanded") === "true";
            botonMenu.setAttribute("aria-expanded", String(!abierto));
            botonMenu.setAttribute(
                "aria-label",
                abierto ? "Abrir menú de navegación" : "Cerrar menú de navegación"
            );
            menu.classList.toggle("abierto", !abierto);
            document.body.classList.toggle("menu-abierto", !abierto);
        });

        menu.querySelectorAll("a").forEach((enlace) => {
            enlace.addEventListener("click", () => {
                botonMenu.setAttribute("aria-expanded", "false");
                botonMenu.setAttribute("aria-label", "Abrir menú de navegación");
                menu.classList.remove("abierto");
                document.body.classList.remove("menu-abierto");
            });
        });
    }

    const comentario = document.querySelector("[data-contador]");
    const totalCaracteres = document.querySelector("[data-total-caracteres]");

    if (comentario && totalCaracteres) {
        const actualizarContador = () => {
            totalCaracteres.textContent = String(comentario.value.length);
        };

        comentario.addEventListener("input", actualizarContador);
        actualizarContador();
    }

    document.querySelectorAll(".alerta--cerrable button").forEach((boton) => {
        boton.addEventListener("click", () => {
            const alerta = boton.closest(".alerta");

            if (alerta) {
                alerta.remove();
            }
        });
    });

    const formulario = document.querySelector(".formulario-contacto");

    if (formulario) {
        formulario.addEventListener("submit", (evento) => {
            const camposRequeridos = formulario.querySelectorAll("[required]");
            let formularioValido = true;

            camposRequeridos.forEach((campo) => {
                if (!campo.checkValidity()) {
                    campo.setAttribute("aria-invalid", "true");
                    formularioValido = false;
                } else {
                    campo.removeAttribute("aria-invalid");
                }
            });

            if (!formularioValido) {
                evento.preventDefault();
                const primerCampoInvalido = formulario.querySelector("[aria-invalid='true']");

                if (primerCampoInvalido) {
                    primerCampoInvalido.focus();
                }
            }
        });
    }
});
