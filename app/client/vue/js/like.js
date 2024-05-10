document.querySelectorAll('.like-button, .dislike-button').forEach(button => {
  button.addEventListener('click', function(event) {
      event.preventDefault();
      var postId = this.getAttribute('data-postid');
      var userId = this.getAttribute('data-userid');
      var isLikeButton = this.classList.contains('like-button');
      var likeCounter = document.querySelector('.like-count[data-postid="' + postId + '"]');
      var dislikeCounter = document.querySelector('.dislike-count[data-postid="' + postId + '"]');
      var actionUrl, counterClass, otherButtonClass;
      if (isLikeButton) {
          actionUrl = '../controler/like.php';
          counterClass = '.like-count';
          otherButtonClass = '.dislike-button';
      } else {
          actionUrl = '../controler/dislik.php';
          counterClass = '.dislike-count';
          otherButtonClass = '.like-button';
      }
      var otherButton = document.querySelector(otherButtonClass + '[data-postid="' + postId + '"]');
      fetch(actionUrl, {
          method: 'POST',
          body: JSON.stringify({ post_id: postId, user_id: userId }),
          headers: { 'Content-Type': 'application/json' }
      })
      .then(response => response.json())
      .then(data => {
          if (data.success) {
              this.classList.toggle(isLikeButton ? 'liked' : 'disliked');

              if (likeCounter) {
                  likeCounter.textContent = data.new_like_count;
              }
              if (dislikeCounter) {
                  dislikeCounter.textContent = data.new_dislike_count;
              }

              if (otherButton) {
                  otherButton.classList.remove(isLikeButton ? 'disliked' : 'liked');
              }
          }
      })
      .catch(error => {
          console.error('Error:', error);
      });
  });
});