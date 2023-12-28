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
