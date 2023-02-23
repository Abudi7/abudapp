// assets/js/likes.js


// Attach event listener to all like buttons
document.querySelectorAll('.like-a').forEach(button => {
    
    button.addEventListener('click', event => {
     
        event.preventDefault();
        const commentId = button.dataset.commentId;
        console.log(commentId);
        const url = `/${commentId}/like`;

        fetch(url, { method: 'POST' })
            .then(response => response.json())
            .then(data => {
                // Update the likes counter for this comment
                const count = document.querySelector(`#comment-${commentId} .like-count`);
                count.textContent = data.likes;
            });
    });
});
