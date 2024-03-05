let toggleBtn = document.querySelectorAll('.form-check-input');

toggleBtn.forEach(function(btn){
    btn.addEventListener('click', function(e){

        axios.get('/api/bibliogame/available/' + btn.value)
        .then(function (response) {

            if(response.data === true){
                document.getElementById('label' + btn.value).innerHTML = "Disponible";
            } else {
                document.getElementById('label' + btn.value).innerText = "Indisponible";
            }
            
        })
        .catch(function (error) {
            
            // console.log(error);
        })

    })
})