document.querySelectorAll('pageLink').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault(); 
        document.getElementById('loadingScreen').style.display = 'block'; 

        setTimeout(() => {
            window.location.href = e.currentTarget.href;
        }, 3000); 
    });
});
