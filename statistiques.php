<?php include "session.php";?>
<?php include 'getlinkpdo.php';?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Statistiques du cabinet</title>
    <link rel="stylesheet" type="text/css" href="styles/styles.css">
    <link rel="stylesheet" type="text/css" href="styles/affichage.css">
    <link rel="stylesheet" type="text/css" href="styles/statistiques.css">
</head>

<body>
    <?php include "header.php";?>

    <main>
        <section class="mainSubject">
            <h2>Statistiques</h2>

            <div id="stats">

            <div>
            <h3>Durée totales des consultations par médecins</h3>

            <table>
              <thead>
                <tr>
                  <th>Médecin</th>
                  <th>Durée consultations</th>
                </tr>
               </thead>
               <tbody>
            <?php
                $res = $linkpdo->prepare("SELECT medecin.idMedecin, medecin.civilite, medecin.nom, medecin.prenom , sum(duree) as dureeTotal FROM consultation, medecin WHERE consultation.idMedecin = medecin.idMedecin GROUP BY medecin.idMedecin UNION SELECT idMedecin, civilite, nom, prenom, 0 as dureeTotal FROM medecin WHERE idMedecin not in (SELECT idMedecin FROM consultation) ORDER BY dureeTotal desc");
                $res->execute();

                while ($data = $res->fetch()) {
                    $idMedecin = $data[0];
                    $medecinString = $data[1]." ".$data[2]." ".$data[3];
                    $sum = $data[4];
            ?>

                <tr>
                    <td><a href="affichage.php?type=medecin&id=<?php echo $idMedecin; ?>"><?php echo $medecinString ?></a></td>
                    <td><?php echo sprintf("%02dh%02dm", intdiv($sum, 60), $sum%60); ?></td>
                </tr>
                <?php
                }
                ?>
                </tbody>
            </table>

        </div>

        
            <div>

                <h3>Répartition des patients</h3>


                <table>
                  <thead>
                    <tr>
                      <th>Tranche d'âge</th>
                      <th>Nombre d'hommes</th>
                      <th>Nombre de femmes</th>
                    </tr>
                   </thead>
                   <tbody>
                <?php
                    $res = $linkpdo->prepare("SELECT
        SUM(CASE WHEN TIMESTAMPDIFF(YEAR, dateNaissance, CURDATE()) < 25 THEN 1 ELSE 0 END) AS moins_de_25_ans,
        SUM(CASE WHEN TIMESTAMPDIFF(YEAR, dateNaissance, CURDATE()) BETWEEN 25 AND 50 THEN 1 ELSE 0 END) AS entre_25_et_50_ans,
        SUM(CASE WHEN TIMESTAMPDIFF(YEAR, dateNaissance, CURDATE()) > 50 THEN 1 ELSE 0 END) AS plus_de_50_ans
    FROM patient
    GROUP BY civilite;");
                    $res->execute();

                    $homme = $res->fetch();
                    $femme = $res->fetch();
                ?>

                    <tr>
                        <td>Moins de 25 ans</td>
                        <td><?php echo $homme[0] ?></td>
                        <td><?php echo $femme[0] ?></td>
                    </tr>

                    <tr>
                        <td>Entre 25 et 50 ans</td>
                        <td><?php echo $homme[1] ?></td>
                        <td><?php echo $femme[1] ?></td>
                    </tr>

                    <tr>
                        <td>Plus de 50 ans</td>
                        <td><?php echo $homme[2] ?></td>
                        <td><?php echo $femme[2] ?></td>
                    </tr>


                    </tbody>
                </table>
            </div>
            

            </div>

        </section>
    </main>

    <?php include "footer.html";?>

</body>
</html>