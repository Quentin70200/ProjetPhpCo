document.addEventListener('DOMContentLoaded', () => {
    const deleteLinks = document.querySelectorAll('.btn-danger');
  
    deleteLinks.forEach(link => {
      link.addEventListener('click', (event) => {
        event.preventDefault();
  
        const confirmation = confirm("Voulez-vous vraiment supprimer ce contact ?");
  
        if (confirmation) {
            const confirmationMessage = "Votre contact sera définitivement supprimer";
            alert(confirmationMessage);
          window.location.href = link.href;
        }
      });
    });
  });