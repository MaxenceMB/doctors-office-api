
const form = document.getElementById("formResearch")

form.addEventListener('submit', (event) => {
	const toulouseFilter = document.getElementById("toulouse")
	const civiliteFilter = document.getElementById("civilite")
	const medecinTraitantFilter = document.getElementById("medecinTraitant")
	const input = document.getElementById("searchinput")

	if (toulouseFilter.options[toulouse.selectedIndex].text === "Indifférent" && 
		toulouseFilter.options[toulouse.selectedIndex].text === "Indifférent" &&
		toulouseFilter.options[toulouse.selectedIndex].text === "Indifférent") {
			alert('test')
	}


})