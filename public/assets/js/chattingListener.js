var chattingElements = document.querySelectorAll('.chatting');
var btnDeleteChatting = document.querySelectorAll('.btnDeleteChatting');
var submit=document.getElementById('submit');
var chattingId=null;
var chattingTo=document.getElementById('chattingTo');
var message=document.getElementById('message');

// foreach chatting in  chatting list 
    chattingElements.forEach(function(chatting){

        chatting.addEventListener('click',function(event){
            chattingId=chatting.getAttribute("value")

            var url=new URL('http://localhost:3000/.well-known/mercure');
            url.searchParams.append('topic','http://localhost/ping/'+chattingId);


            const eventSource = new EventSource(url);
            
            eventSource.onmessage = e => showMessage(e.data,eventSource,chattingId);
           
               show(chatting.getAttribute("value"));
               submit.disabled = false;
               message.disabled = false;
               chattingTo.innerHTML = 'Ecrire à ' + chatting.getAttribute('userTo');
               
            })
    })
    
//sending message pressing enter key      
    document.getElementById('message').addEventListener('keypress',function(event){

        var userAlias=document.getElementById('userAlias');
        if(document.getElementById("message").value !='' && event.key === 'Enter'){
            if(document.getElementById('trash')){document.getElementById("trash").remove()}
                send();
            }
        })

        
//sending message clicking on "envoyer"
    document.getElementById('submit').addEventListener('click',function(){
        var userAlias = document.getElementById('userAlias');

        if(document.getElementById("message").value !=''){
                
                axios.post('/api/test',{
                    message:document.getElementById("message").value,
                    chatting:chattingId,
                    alias:document.getElementById('userAlias').getAttribute('value'),
                    userId:document.getElementById('userToId').getAttribute('value')
                }).then(function(response){
                    document.getElementById('message').value="";
                    //show(chattingId);

                })
            }
        })

        btnDeleteChatting.forEach(function(btnDelete){

            btnDelete.addEventListener('click',function(event){
                    
                   deleteChatting(btnDelete.getAttribute('value'));
                })
        })

