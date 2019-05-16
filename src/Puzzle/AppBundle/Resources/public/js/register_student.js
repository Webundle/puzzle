//Fonction pour l'inscription d'un étudiant à un concours
$(document).ready(function()
{
	//alert("oK C EST PRET");
	var nom=$("#nom").val();
	var prenom=$("#prenoms").val();
	var contact=$("#contact").val();
	var email=$("#email").val();
	var adresse=$("#adresse").val();

	//var dataForm=$("#formulaire");
	//Clic sur le bouton
	$("#formulaire").submit(function(e)
	{
		e.preventDefault();
		alert("Ok on vient de cliquer");
		console.log("Var "+contact);
		console.log("Var "+nom);
		console.log("Var email "+email);
		console.log("Var teste "+adresse);
		$.ajax({
			url:"http://localhost:8000/inscription/register-ajax",
			type:'POST',
			//data:dataForm.serialize(),
			dataType: "JSON",
			//Avant l'envoie
			beforeSend:function(){
				$(".response").html("<img src='http://localhost:8000/assets/img/spinner.gif'/>");
				console.log('before');
			},
			//En cas de succès
			success:function(data)
			{
				console.log(data);
			},
			//En cas d'erreur
			error: function(error)
			{
				console.log(error);
			}
		});
	});
})