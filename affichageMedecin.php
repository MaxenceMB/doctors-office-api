<!-- ----------------------------------------------------- -->
<!-- MEDECIN: ONGLET MEDECIN                               -->
<!-- ----------------------------------------------------- -->
<div style="display:<?php echo $type=='medecin' ? 'block': 'none'?>" class="Medecin" id="formMedecin">
    <!-- ----------------------------------------------------- -->
    <!-- MEDECIN: FORMULAIRE DE RECHERCHE                      -->
    <!-- ----------------------------------------------------- -->
    <form class="research" onsubmit="return checkValidMedecin()" method="post" action="affichage.php?type=medecin">
        <div class="flex-research">
            <div class="searchinput">
                <label for="searchinput">Recherche avancé</label>
                <input name="searchinput" id="searchinputMedecin" value="<?php echo (isset($_POST['rechercherMedecin'])) ? $_POST['searchinput'] : '' ?>" placeholder="Recherchez un médecin ici  (Optionnel)">
            </div>

            <div class="medecinTraitant">
                <label for="medecinTraitant">Filtre médecin</label>
                <select name="medecinTraitant" id="medecinTraitantMedecin">
                    <option>Indifférent</option>
                    <?php
                        $resMedecinString = $linkpdo->prepare("SELECT nom, prenom, civilite, idMedecin FROM medecin ORDER BY nom");
                        $resMedecinString->execute();

                        while ($data = $resMedecinString->fetch()) {
                            $string = $data[0]." ".$data[1]." (".$data[2].")";
                            $idMedecinT = $data[3];
                    ?>


                    <option value="<?php echo $idMedecinT?>" <?php echo isset($_POST['rechercherMedecin']) ? ($_POST['medecinTraitant'] == $idMedecinT ? 'selected' : '') : (isset($_GET['id']) ? ($_GET['id'] == $idMedecinT ? 'selected' : '') : ''); ?>><?php echo $string; ?></option>

                    <?php
                    }
                    ?>
                </select>
            </div>

            <div class="civilite">
                <label for="civilite">Filtre civilité</label>
                <select name="civilite" id="civiliteMedecin">
                    <option>Indifférent</option>
                    <option <?php echo (isset($_POST['rechercherMedecin'])) ? ($_POST['civilite'] == 'M.' ? 'selected' : '') : '' ?>>M.</option>
                    <option <?php echo (isset($_POST['rechercherMedecin'])) ? ($_POST['civilite'] == 'Mme' ? 'selected' : '') : '' ?>>Mme</option>
                </select>
            </div>
        </div>

        <div class="submit">
            <input onclick="fromButtonSearch=true;" <?php echo isset($_GET['id']) ? "id=openFormImmediatly" : "" ?> type="submit" name="rechercherMedecin" value="" class="btna blue" id="confirm">
            <input onclick="fromButtonSearch=false;" type="submit" name="reset" value="" class="btna blue" id="reset" formnovalidate>
        </div>
    </form>

    <!-- ----------------------------------------------------- -->
    <!-- MEDECIN: LISTE DES MEDECINS EN RESULTAT DE RECHERCHE  -->
    <!-- ----------------------------------------------------- -->
    <div class="liste-usagers">

    <?php
    // ----------------------------------------------------------------------------------------------
    // MEDECIN: CAS 2 : On fait une recherche (POST) dans l'onglet médecin
    // ----------------------------------------------------------------------------------------------
    if (isset($_POST['rechercherMedecin'])) {
        $recherche = explode(" ", $_POST['searchinput']);

        $where_requete = "";
        $where_lst = array();
        foreach ($recherche as $key => $value) {
            $where_requete .= (($key == 0) ? " WHERE (" : " OR")." nom LIKE :keyword$key OR prenom LIKE :keyword$key OR civilite LIKE :keyword$key";
            $where_lst["keyword$key"] = "%$value%";
        }

        $where_requete .= ")";

        if ($_POST["medecinTraitant"] != "Indifférent") {
            $where_requete .= " AND idMedecin = :idMedecin";
            $where_lst["idMedecin"] = $_POST['medecinTraitant'];
        }

        if ($_POST["civilite"] != "Indifférent") {
            $where_requete .= " AND civilite = :civilite";
            $where_lst["civilite"] = $_POST['civilite'];
        }

        $res = $linkpdo->prepare("SELECT * FROM medecin".$where_requete." ORDER BY nom");
        $res->execute($where_lst);

        if ($res->rowcount() == 0) {
        ?>
        <p class="nbResultat">Aucun résultat</p>
        <?php
        
        } else {
        
        ?>
        <p class="nbResultat"><?php echo $res->rowcount() ?> résultat(s)</p>
        <?php
        }

    // ----------------------------------------------------------------------------------------------
    // MEDECIN: CAS 3 : Premier arrivée sur la page (GET), on affiche un aperçu de la table médecin
    // ----------------------------------------------------------------------------------------------
    } else {
        // ----------------------------------------------------------------------------------------------
        // PATIENT: CAS 3.1 : La requête GET provient d'une suppression d'un patient
        // ----------------------------------------------------------------------------------------------
        $res = $linkpdo->prepare("SELECT * FROM medecin ORDER BY nom");
        $res->execute();

        if (isset($_GET['medecinSuppr'])) {
            if ($_GET['medecinSuppr'] == "error") {
        ?>
        <p class="nbResultat nbResultatRed">❌ Une erreur s'est produite lors de la suppression du médecin</p>
        <?php 
    } else {
        ?>
        <p class="nbResultat nbResultatGreen">✔️ Le médecin a bien été supprimé</p>
        <?php 
        }} else {
        ?>
        <p class="nbResultat">Voici la liste des <?php echo $res->rowcount() ?> médecins du cabinet médical</p>
    <?php } 
} ?>


    <!-- DANS LES 2 CAS -->
        <div id="createButton">
            <a href="ajout.php?type=medecin" class="btna blue">
                Ajouter un médecin
            </a>
        </div>
        <?php
        


        while ($data = $res->fetch()) {
            $resCountPatientAssocie = $linkpdo->prepare("SELECT count(*) FROM patient WHERE idMedecin = :idMedecin");
            $resCountPatientAssocie->execute(array('idMedecin' => $data['idMedecin']));
            $countPatient = $resCountPatientAssocie->fetch()[0];
    ?>

        <div>
            <div class="first-part">
                <p class="name"><?php echo $data['civilite']." ".$data['nom']." ".$data['prenom'] ?></p>
                <p class="countPatient"><span class="label">Patients attitrés</span><?php echo $countPatient ?><?php if ($countPatient != 0) {?> <span class="detail">(</span><a href="affichage.php?type=patient&idMedecin=<?php echo $data['idMedecin']?>" class="detail">voir la liste</a><span class="detail">)</span><?php }?></p>
            </div>
            <div class="second-part">
                <button class="btna bluenoshadow">Modifier</button>
                <button onclick="deleteMedecin(this)" data-patient-id="<?php echo $data['idMedecin']; ?>" class="btna rednoshadow">Supprimer</button>
            </div>
        </div>
        <?php
        }
        ?>
    </div>
</div>