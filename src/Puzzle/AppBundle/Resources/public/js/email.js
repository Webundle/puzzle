$(document).ready(function()
{
	//alert("Ok jQuery is read");
	//Fonction de souscription
	var souscrire=$("#thesubmit");
	var email=$("#email").val();
	var data=$("#thesubmit");
	souscrire.submit(function(event)
	{
		//alert( "Handler for .submit() called." );
		//On bloque l'action par défaut
		event.preventDefault();
		$.ajax({
			url:'http://localhost:8000/newsletter/new',
			type:'POST',
			data: data.serialize(),
			dataType: 'text',
			beforeSend:function(){
				$(".response").html("Patientez <img src='http://localhost:8000/assets/img/spinner.gif'/>");
				console.log('before');
			},
			success:function(data){
				console.log("Succès "+data);
				if(data=="Ok"){
					$(".response").html('<p>Souscription effectuée avec succès</p>');
				}
			},
			error:function()
			{
				console.log("Erreur");
				$(".response").html('Souscription déjà effectuée ou données incorrectes');
			}
		});
	});
});