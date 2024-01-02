var fromButtonSearch;

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
			document.getElementsByClassName("nbResultat")[0].innerText = "❌ Merci de renseigner une recherche"
			document.getElementsByClassName("nbResultat")[0].style.color = "red"
			return false;
	}

	document.getElementsByClassName("nbResultat")[0].style.color = "none"
	return true
}



function deleteConsultation(button) {
	const backgroundToBlur = document.querySelectorAll('main *:not(#suppression)');

	backgroundToBlur.forEach(element => {
	    element.style.filter = 'blur(0.5rem)';
	});
	document.getElementById("suppression").style.display = "block"
	document.getElementById("consultationPatientASupprimer").value = button.getAttribute('data-patient-id');
	document.getElementById("consultationMedecinASupprimer").value = button.getAttribute('data-medecin-id');
	document.getElementById("consultationDateRDV").value = button.getAttribute('data-daterdv');
	document.getElementById("consultationHeureRDV").value = button.getAttribute('data-heurerdv');
}

function annulationSuppression(button) {
	const backgroundToBlur = document.querySelectorAll('main *:not(#suppression)');

	backgroundToBlur.forEach(element => {
	    element.style.filter = 'none';
	});

	document.getElementById("suppression").style.display = "none"
	// pas besoin de remettre toutes les values à "", elles seront réassigné (si l'utilisateur remet #suppression en display block et valide c'est sa faute)
}