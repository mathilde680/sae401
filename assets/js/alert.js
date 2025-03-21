import Swal from 'sweetalert2';

document.querySelectorAll(".deleteButton").forEach(form => {
    form.addEventListener("submit", (event) => {
        event.preventDefault(); // Empêche la soumission automatique du formulaire

        Swal.fire({
            title: "Êtes-vous sûr ?",
            text: "Vous vous apprêtez à supprimer quelque chose.",
            showCancelButton: true,
            cancelButtonText: "Annuler",
            confirmButtonText: "Supprimer",
            customClass: {
                popup: "custom-style",
                cancelButton: "btn-cancel",
                confirmButton: "btn-confirm",
            }
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            } else {
                Swal.fire("Annulé", "Aucune suppression effectuée.");
            }
        });
    });
});




