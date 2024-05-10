document.querySelectorAll('.save-button').forEach(button => {
    button.addEventListener('click', function(event) {
        event.preventDefault();
        var postId = this.getAttribute('data-postid');
        var userId = this.getAttribute('data-userid');

        fetch('../controler/enregistrer.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'userId=' + userId + '&postId=' + postId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Publication enregistrée');
                this.classList.toggle('saved');
            } else {
                console.log(data.message ? data.message : 'Erreur');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
        });
    });
});