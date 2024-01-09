function showTab(nom) {
    window.history.replaceState({}, document.title, window.location.pathname + '?type='+nom.toLowerCase());
    current = document.getElementById("current")
    buttons = document.getElementById("tabs").children

    if(current.textContent != nom) {
        // Inverse le current des boutons

        // Inverse l'affichage
        itemsPatients = Array.from(document.getElementsByClassName("Patient"))
        itemsMedecins = Array.from(document.getElementsByClassName("Medecin"))
        itemsConsultations = Array.from(document.getElementsByClassName("Consultation"))

        if(nom == "Patient") {
            buttons[0].id = "current";
            buttons[1].id = "none";
            buttons[2].id = "none";
            itemsPatients.forEach((item) => {
                item.style.display = "block"
            })

            itemsMedecins.forEach((item) => {
                item.style.display = "none"
            })
            itemsConsultations.forEach((item) => {
                item.style.display = "none"
            })
            document.getElementById("formPatient").style.display = "block";
        } else if(nom == "Medecin") {
            buttons[0].id = "none";
            buttons[1].id = "current";
            buttons[2].id = "none";
            itemsMedecins.forEach((item) => {
                item.style.display = "block"
            })


            itemsPatients.forEach((item) => {
                item.style.display = "none"
            })

            itemsConsultations.forEach((item) => {
                item.style.display = "none"
            })

            
            document.getElementById("formMedecin").style.display = "block";
        } else if(nom == "Consultation") {
            buttons[0].id = "none";
            buttons[1].id = "none";
            buttons[2].id = "current";
            itemsConsultations.forEach((item) => {
                item.style.display = "block"
            })

            itemsMedecins.forEach((item) => {
                item.style.display = "none"
            })
            itemsPatients.forEach((item) => {
                item.style.display = "none"
            })
            document.getElementById("formConsultation").style.display = "block";
        }
    }
}