function showTab(nom) {
    window.history.replaceState({}, document.title, window.location.pathname + '?type='+nom.toLowerCase());
    current = document.getElementById("current")
    buttons = document.getElementById("tabs").children

    if(current.textContent != nom) {
        // Inverse le current des boutons
        if(current == buttons[0]) {
            buttons[0].id = "none"
            buttons[1].id = "current"
        } else {
            buttons[0].id = "current"
            buttons[1].id = "none"
        }

        // Inverse l'affichage
        itemsPatients = Array.from(document.getElementsByClassName("Patient"))
        itemsMedecins = Array.from(document.getElementsByClassName("Medecin"))

        if(nom == "Patient") {
            itemsPatients.forEach((item) => {
                item.style.display = "block"
            })
            document.getElementById("formPatient").style.display = "block";

            itemsMedecins.forEach((item) => {
                item.style.display = "none"
            })
        } else if(nom == "Medecin") {
            itemsPatients.forEach((item) => {
                item.style.display = "none"
            })

            itemsMedecins.forEach((item) => {
                item.style.display = "block"
            })
            document.getElementById("formMedecin").style.display = "block";
        }
    }
}