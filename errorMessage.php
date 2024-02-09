<?php
function showMessage($type, $message) {
    echo "<div id='message' class='$type'>         
        <p>$message</p>
        <button class='button-supprimer' onclick=\"document.getElementById('message').style.display = 'none'\"></button>
    </div>";
}

$correspondanceMessage = [
    "errorRecherche" => ["messageError", "Une erreur innatendue s'est produite lors de la recherche"],
    "patientSupprError" => ["messageError", "Une erreur s'est produite lors de la suppression du patient"],
    "patientSupprSuccess" => ["messageSuccess", "Le patient a bien été supprimé"],
    "medecinSupprError" => ["messageError", "Une erreur s'est produite lors de la suppression du médecin"],
    "medecinSupprSuccess" => ["messageSuccess", "Le médecin a bien été supprimé"],
    "consultationSupprError" => ["messageError", "Une erreur s'est produite lors de la suppression de la consultation"],
    "consultationSupprSuccess" => ["messageSuccess", "La consultation a bien été supprimée"],
    "NOM_VIDE" => ["messageError", "Le nom est vide"],
    "NOM_LONG" => ["messageError", "Le nom ne peut pas dépasser 20 caractères"],
    "NOM_SPE" => ["messageError", "Le nom contient des charactères interdits."],
    "PRENOM_VIDE" => ["messageError", "Le prénom est vide"],
    "PRENOM_LONG" => ["messageError", "Le prénom ne peut pas dépasser 20 caractères"],
    "PRENOM_SPE" => ["messageError", "Le prénom contient des charactères interdits."],
    "SECU_INV" => ["messageError", "Le numéro de sécurité est invalide"],
    "CIV_INV" => ["messageError", "Civilité invalide"],
    "ADR_VIDE" => ["messageError", "Civilité invalide"],
    "ADR_LONG" => ["messageError", "Civilité invalide"],
];

if (isset($_GET['message'])) {
    if (array_key_exists($_GET['message'], $correspondanceMessage)) {
        $correspondance = $correspondanceMessage[$_GET['message']];
        showMessage($correspondance[0], $correspondance[1]);
    }
}
?>  