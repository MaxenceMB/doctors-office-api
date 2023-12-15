

var resetOrSearchClicked;

function checkValidPatient() {
	const toulouseFilter = document.getElementById("toulouse")
	const civiliteFilter = document.getElementById("civilite")
	const medecinTraitantFilter = document.getElementById("medecinTraitant")
	const input = document.getElementById("searchinput")

	if (resetOrSearchClicked === ) {

	}

	if (toulouseFilter.options[toulouseFilter.selectedIndex].text === "Indifférent" && 
		civiliteFilter.options[civiliteFilter.selectedIndex].text === "Indifférent" &&
		medecinTraitantFilter.options[medecinTraitantFilter.selectedIndex].text === "Indifférent" && 
		input.value === "") {
			document.getElementsByClassName("nbResultat")[0].innerText = "Merci de renseigner une recherche"
			document.getElementsByClassName("nbResultat")[0].style.color = "red"
			return false;	
	}
	document.getElementsByClassName("nbResultat")[0].style.color = "none"
	return true
}

function checkValidMedecin() {
	const civiliteFilter = document.getElementById("civilite")
	const input = document.getElementById("searchinput")

	if (civiliteFilter.options[civiliteFilter.selectedIndex].text === "Indifférent" &&
		input.value === "") {
			document.getElementsByClassName("nbResultat")[0].innerText = "Merci de renseigner une recherche"
			document.getElementsByClassName("nbResultat")[0].style.color = "red"
			return false;	
	}
	document.getElementsByClassName("nbResultat")[0].style.color = "none"
	return true
}