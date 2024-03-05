// Reload #myBibliogame 
function loadBibliogame(){
    // get games from my bibliogame
    axios.get('/api/bibliogame')
    .then(function (response) {
        // console.log(response.data);

        // get and empty #mybibliogame
        let b = document.getElementById('myBibliogame');
        b.innerHTML ="";

        response.data.forEach(function(element){
            // ceate a new div
            let div = document.createElement('div');
            // add class
            div.classList.add('col-md-3','mb-3');
            // put html content in div
            div.innerHTML = element.html.content;
            // set div as a child of mybibliogame
            b.appendChild(div);

        })

        // add listeners on action buttons
        actionButton();
    })
    .catch(function (error) {
        console.log(error);
    })
}