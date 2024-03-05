// send a message 
function send(){

    axios.post('/api/messagerie/send/', {
        chatting:chattingId,
        messageSent:message = userAlias.value + ' dit : ' + document.getElementById("message").value
    })
    .then(function(response){
       document.getElementById('message').value = "";
       show(chattingId);
    })
    .catch(function(error){
        console.log(error)
    })
}

//refresh the chatting ; new message appears 
function show(id){

    axios.get('/api/messagerie/'+id)
    .then(function(response){
        var chatFrame=document.getElementById('chatFrame');

        chatFrame.innerHTML="";
        var messages=response.data;
        chattingId=id;

        for (let i = 0; i < messages.length; i++) {
            el = document.createElement('p');
            el.innerHTML = messages[i].content;
            chatFrame.appendChild(el);
        }
    })
    .catch(function(error){
        console.log(error)
    })
        
}

function deleteChatting(id){
    axios.post('/api/messagerie/supprimer',{
        chattingId:id
    })
    .then(function(response){
        console.log(response);
    })
}


function showMessage(messages,eventSource,chattingId){
    var chatFrame=document.getElementById('chatFrame');

    // const match = eventSource.url.match(/http:\/\/localhost:3000\/.well-known\/mercure?topic=http%3A%2F%2Flocalhost%2Fping%2F(\d*)/);
    // console.log(match)
    // if (match) {
    //     const numberAfterLastSlash = match[1];
    //     console.log(numberAfterLastSlash);}

var numberAfterLastSlash=(eventSource.url).charAt(eventSource.url.length-1 )
    if(numberAfterLastSlash===chattingId){

       // console.log(eventSource.url)
        chatFrame.innerHTML="";
        messages=JSON.parse(messages)
        for (let i = 0; i < messages.length; i++) {
            el = document.createElement('p');
            el.innerHTML = messages[i];
            chatFrame.appendChild(el);
       
    }
    }
   

}

