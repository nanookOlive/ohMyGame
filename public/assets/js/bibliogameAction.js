// add listeners onload page
actionButton();

function actionButton(){
    // Add/Remove buttons
    let actionBtn = document.querySelectorAll('.btn-action');

    actionBtn.forEach(function(btn){
        // Event on click action button
        btn.addEventListener('click', function(e){
            // change status of game in bibliogame
            axios.post('/api/bibliogame/action', {id: btn.value})
            .then(function (response) {
                console.log(response.data);

                // If response has null data, game was remove from my bibliogame
                if (response.data === null) {
                    // If I'm on bibliogame page
                    if(document.getElementById('myBibliogame')) {
                        // Because a game can be in myBibliogame and in borrowedGames at same time
                        // Get all buttons with same id
                        idemBtn = document.querySelectorAll('#action' + btn.value);
                        // Change content of buttons
                        idemBtn.forEach(element => {
                            element.innerHTML ='<i class="fa-solid fa-plus"></i> Ajouter';
                            element.classList.remove("btn-light");
                            element.classList.add("btn-success");
                        });

                        // Get card id corresponding to the game and remove the card
                        if(document.getElementById('card' + btn.value)){
                            document.getElementById('card' + btn.value).remove();
                        }

                        // Reload #myBibliogame
                        loadBibliogame();

                    // I'm not on bibliogame page
                    } else {
                        // Game was remove from my bibliogame, change button content
                        let button = document.getElementById('action' + btn.value);
                        button.innerHTML = '<i class="fa-solid fa-plus"></i> Ajouter';
                        button.classList.remove("btn-light");
                        button.classList.add("btn-success");
                    }
                // Response has data, game was added to my bibliogame
                } else {
                    // Change button content
                    let button = document.getElementById('action' + btn.value);
                    button.innerHTML = '<i class="fa-solid fa-minus"></i> Retirer';
                    button.classList.remove("btn-success");
                    button.classList.add("btn-light");
                }

            })
            .catch(function (error) {
                console.log(error);
            })
        })
    })
}