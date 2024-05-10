function loadMessages() {
    const amiId = '<?php echo addslashes($amiActuelId); ?>';
    $.ajax({
        url: '../controler/recuperer_message.php?ami=' + amiId,
        method: 'GET',
        dataType: 'json',
        success: function (data) {
            const messagesContainer = $('.messages');
            messagesContainer.empty();

            if (data.length === 0) {
            messagesContainer.append("<div class='no-messages'>Vous n'avez pas échangé de message encore.</div>");
            } else {
            let currentDate = '';
            data.forEach(function (message) {
                if (message.date !== currentDate) {
                    currentDate = message.date;
                    messagesContainer.append(`<div class='date-separator'>${currentDate}</div>`);
                }

                const messageClass = message.sender_id == amiId ? 'message-recu' : 'message-envoye';
                const messageElement = `
                    <div class="message ${messageClass}">
                        <div class="message-text">${message.text}</div>
                        <div class="message-time">${message.time}</div>
                    </div>
                `;
                messagesContainer.append(messageElement);
            });
        }}
    });
}




document.addEventListener('DOMContentLoaded', (event) => {
    loadMessages();

    document.querySelector('.envoyer-message form').addEventListener('submit', function(e) {
        e.preventDefault(); 

        const form = this;
        const data = new FormData(form);

        fetch('../controler/envoyer_message.php', {
            method: 'POST',
            body: data
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                form.querySelector('textarea').value = '';
                loadMessages(); 
            } else {
                alert(data.error);
            }
        })
        .catch((error) => {
            console.error('Erreur lors de l\'envoi du message:', error);
        });
    });
});

