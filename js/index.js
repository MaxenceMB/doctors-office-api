function showPassword(button) {
	let inputPassword = document.getElementById("password")


	// si l'input est de type password et l'oeil est fermé
	if (button.children[0].src.includes("oeilFerme")) {
		button.children[0].src = "images/oeilOuvert.png"
		inputPassword.type = "text";
	}
	// sinon si l'input est de type text et l'oeil est ouvert
	else { // if (eyesClasses.contains("fa-eye")) (and other)
		button.children[0].src = "images/oeilFerme.png" // pourquoi on ne met pas ../images/oeilOuvert.png ?? ca dépend du contexte de la page html qui appele le js
		inputPassword.type = "password";
	}
}