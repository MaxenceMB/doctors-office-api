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

function checkValidConsultation() {
	if (!fromButtonSearch) {
		return true;
	}

	const startDateFilter = document.getElementById("startDate")
	const endDateFilter = document.getElementById("endDate")
	const startHoursFilter = document.getElementById("startHours")
	const endHoursFilter = document.getElementById("endHours")
	const startDureeFilter = document.getElementById("startDuree")
	const endDureeFilter = document.getElementById("endDuree")


	const medecinConsultationFilter = document.getElementById("medecinConsultation")
	const patientConsultationFilter = document.getElementById("patientConsultation")

	if (startDateFilter.value === "" &&
		endDateFilter.value === "" &&
		startHoursFilter.value === "" &&
		endHoursFilter.value === "" &&
		startDureeFilter.options[startDureeFilter.selectedIndex].text === "Indifférent" &&
		endDureeFilter.options[endDureeFilter.selectedIndex].text === "Indifférent" &&
		medecinConsultationFilter.options[medecinConsultationFilter.selectedIndex].text === "Indifférent" &&
		patientConsultationFilter.options[patientConsultationFilter.selectedIndex].text === "Indifférent"
		) {
			document.getElementsByClassName("nbResultat")[2].innerText = "❌ Merci de renseigner une recherche"
			document.getElementsByClassName("nbResultat")[2].style.color = "red"
			return false;
	}

	document.getElementsByClassName("nbResultat")[0].style.color = "none"
	return true
}




function deletePatient(button) {
	const backgroundToBlur = document.querySelectorAll('main *:not(#suppression)');

	backgroundToBlur.forEach(element => {
	    element.style.filter = 'blur(0.5rem)';
	});

	let supprParagraph = document.getElementById("suppression");
	supprParagraph.style.display = "block"
	supprParagraph.children[0].innerText = "Voulez-vous vraiment supprimer ce patient ? Cela engendrera la suppression de ses consultations s'il en possède.";

	document.getElementById("patientSuppr").name = "idPatient";
	document.getElementById("patientSuppr").value = button.getAttribute('data-patient-id');
}

function deleteMedecin(button) {
	const backgroundToBlur = document.querySelectorAll('main *:not(#suppression)');

	backgroundToBlur.forEach(element => {
	    element.style.filter = 'blur(0.5rem)';
	});

	let supprParagraph = document.getElementById("suppression");
	supprParagraph.style.display = "block"
	supprParagraph.children[0].innerText = "Voulez-vous vraiment supprimer ce médecin ? Cela engendrera la suppression de ses consultations s'il en possède et certains patients n'auront plus de médecin traitant.";

	document.getElementById("medecinSuppr").name = "idMedecin";
	document.getElementById("medecinSuppr").value = button.getAttribute('data-patient-id');
}


function deleteConsultation(button) {
	const backgroundToBlur = document.querySelectorAll('main *:not(#suppression)');

	backgroundToBlur.forEach(element => {
	    element.style.filter = 'blur(0.5rem)';
	});

	let supprParagraph = document.getElementById("suppression");
	supprParagraph.style.display = "block"
	supprParagraph.children[0].innerText = "Voulez-vous vraiment supprimer cette consultation ?";

	document.getElementById("patientSuppr").name = "idPatient";
	document.getElementById("patientSuppr").value = button.getAttribute('data-patient-id');
	document.getElementById("medecinSuppr").name = "idMedecin";
	document.getElementById("medecinSuppr").value = button.getAttribute('data-medecin-id');
	document.getElementById("consultationDateRDV").name = "dateRDV";
	document.getElementById("consultationDateRDV").value = button.getAttribute('data-daterdv');
	document.getElementById("consultationHeureRDV").name = "heureRDV";
	document.getElementById("consultationHeureRDV").value = button.getAttribute('data-heurerdv');
}

function annulationSuppression(button) {
	const backgroundToBlur = document.querySelectorAll('main *:not(#suppression)');

	backgroundToBlur.forEach(element => {
	    element.style.filter = 'none';
	});

	document.getElementById("suppression").style.display = "none"

	document.getElementById("patientSuppr").name = "";
	document.getElementById("patientSuppr").value = "";
	document.getElementById("medecinSuppr").name = "";
	document.getElementById("medecinSuppr").value = "";
	document.getElementById("consultationDateRDV").name = "";
	document.getElementById("consultationDateRDV").value = "";
	document.getElementById("consultationHeureRDV").name = "";
	document.getElementById("consultationHeureRDV").value = "";
}