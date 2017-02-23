$(function()
{
	// on crée une variable pour stocker s'il y au moins une erreur
	var error = false;

	$('#bouton').prop('disabled', true);

	// on attache un écouteur sur l'évenement (quitter le champ) pour les champs requis input
	$('input[required]').on('blur', function(){
		// on crée une variable pour stocker la valeur de l'input
		var valInput = $(this).val();
		// on crée une variable pour stocker le nombre de caractère
		var lengthInput = valInput.length;
		
		// on teste si le nombre de caractère est inférieur à 2
		if(lengthInput < 3){
			// on ajoute la classe error au champ concerné
			$(this).addClass('error');
			// on a une erreur, on active notre variable
			error = true;
		} else {
			// on retire la classe error du champ concerné
			$(this).removeClass('error');
		}
	});
		// on attache un écouteur sur l'évenement (quitter le champ) pour le champs requis select
		$('select[required]').on('blur', function(){
			if($('select').val() == "" ){
				$('select').addClass('error');
				error = true;
			}
			else{
				$(this).removeClass('error');
			}
		});

		if(!error){
			$('#bouton').prop('disabled', false);
		}
});
