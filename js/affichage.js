document.addEventListener('DOMContentLoaded', function() {
    if (window.location.search === "") {
        window.history.replaceState({}, document.title, window.location.pathname + '?type=patient');
    }
});


// bouton input submit pour rechercher : utilisé pour ouvrir le "voir les patients" dans l'onglet médecin, pour faire une requête post pour voir la liste
const openFormImmediatlyElement = document.getElementById('openFormImmediatly')

// formulaire lancé au lancement de la page 
if (openFormImmediatlyElement !== null) {
	openFormImmediatlyElement.click();
}

var fromButtonSearch;

function checkValidPatient() {
	if (!fromButtonSearch) {
		return true;
	}
	const toulouseFilter = document.getElementById("toulouse")
	const civiliteFilter = document.getElementById("civilitePatient")
	const medecinTraitantFilter = document.getElementById("medecinTraitant")
	const input = document.getElementById("searchinputPatient")

	if (toulouseFilter.options[toulouseFilter.selectedIndex].text === "Indifférent" && 
		civiliteFilter.options[civiliteFilter.selectedIndex].text === "Indifférent" &&
		medecinTraitantFilter.options[medecinTraitantFilter.selectedIndex].text === "Indifférent" && 
		input.value === "") {
			document.getElementsByClassName("nbResultat")[0].innerText = "❌ Merci de renseigner une recherche"
			document.getElementsByClassName("nbResultat")[0].style.color = "red"
			return false;
	}
	document.getElementsByClassName("nbResultat")[0].style.color = "none"
	return true
}

function checkValidMedecin() {
	if (!fromButtonSearch) {
		return true;
	}

	const civiliteFilter = document.getElementById("civiliteMedecin")
	const medecinTraitantFilter = document.getElementById("medecinTraitantMedecin")
	const input = document.getElementById("searchinputMedecin")
	if (civiliteFilter.options[civiliteFilter.selectedIndex].text === "Indifférent" &&
		medecinTraitantFilter.options[medecinTraitantFilter.selectedIndex].text === "Indifférent" && 
		input.value === "") {
			document.getElementsByClassName("nbResultat")[1].innerText = "❌ Merci de renseigner une recherche"
			document.getElementsByClassName("nbResultat")[1].style.color = "red"
			return false;	
	}

	document.getElementsByClassName("nbResultat")[1].style.color = "none"
	return true
}



function deletePatient(button) {
	const backgroundToBlur = document.querySelectorAll('main *:not(#suppression)');

	backgroundToBlur.forEach(element => {
	    element.style.filter = 'blur(0.5rem)';
	});
	document.getElementById("suppression").style.display = "block"
	let id = button.getAttribute('data-patient-id');
	document.getElementById("personneASupprimer").name = "idPatient";
	document.getElementById("personneASupprimer").value = id;
}

function deleteMedecin(button) {
	const backgroundToBlur = document.querySelectorAll('main *:not(#suppression)');

	backgroundToBlur.forEach(element => {
	    element.style.filter = 'blur(0.5rem)';
	});
	document.getElementById("suppression").style.display = "block"
	let id = button.getAttribute('data-patient-id');
	document.getElementById("personneASupprimer").name = "idMedecin";
	document.getElementById("personneASupprimer").value = id;
}

function annulationSuppression(button) {
	const backgroundToBlur = document.querySelectorAll('main *:not(#suppression)');

	backgroundToBlur.forEach(element => {
	    element.style.filter = 'none';
	});

	document.getElementById("suppression").style.display = "none"
	document.getElementById("personneASupprimer").name = "";
	document.getElementById("personneASupprimer").value = "";
}