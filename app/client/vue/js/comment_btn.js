document.querySelectorAll('.comment-button').forEach(button => {
    button.addEventListener('click', function(event) {
      event.preventDefault();
      var postId = this.getAttribute('data-postid');
      var replySection = document.getElementById('reply-section-' + postId);
      if (replySection.classList.contains('visible')) {
          replySection.classList.remove('visible');
          setTimeout(() => {
              replySection.style.display = 'none';
          }, 300);
      } else {
          replySection.style.display = 'block';
          window.requestAnimationFrame(function() {
              replySection.classList.add('visible');
          });
      }
    });
  });