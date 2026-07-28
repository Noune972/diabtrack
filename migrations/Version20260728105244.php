<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250101000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Import des données Ciqual dans la table aliment_reference';
    }

    public function up(Schema $schema): void
    {
        $csvPath = __DIR__ . '/../Documentation/ciqual.csv';

        if (!file_exists($csvPath)) {
            throw new \RuntimeException('Fichier CSV introuvable : ' . $csvPath);
        }

        $handle = fopen($csvPath, 'r');

        // ⚠️ Le fichier Ciqual utilise la virgule comme délimiteur ET comme séparateur décimal
        // (ex: "4,41"), mais comme les champs numériques sont entre guillemets,
        // fgetcsv() gère ça correctement : chaque champ quoté = une seule valeur.
        $delimiter = ',';

        // On saute la ligne d'en-tête (peut s'étaler sur plusieurs lignes physiques
        // à cause des retours à la ligne dans les guillemets — fgetcsv gère ça nativement)
        fgetcsv($handle, 0, $delimiter);

        // Positions fixes des colonnes qui nous intéressent, d'après la structure réelle du fichier :
        // index 7  = alim_nom_fr (nom de l'aliment)
        // index 10 = Energie, Règlement UE N°1169/2011 (kcal/100g)
        $indexNom = 7;
        $indexKcal = 10;

        $count = 0;

        while (($ligne = fgetcsv($handle, 0, $delimiter)) !== false) {
            $nom = $ligne[$indexNom] ?? null;
            $energieBrute = $ligne[$indexKcal] ?? null;

            if (!$nom) {
                continue;
            }

            // Normalise la valeur numérique (remplace la virgule décimale par un point)
            $energieNettoyee = str_replace(',', '.', (string) $energieBrute);
            $energieNettoyee = trim($energieNettoyee);

            // Ignore les lignes sans valeur exploitable (ex: "-", "traces", "< 20")
            if (!is_numeric($energieNettoyee)) {
                continue;
            }

           $nomEchappe = str_replace("'", "''", trim($nom));
            $energieFormatee = (float) $energieNettoyee;

            $this->addSql(
                "INSERT INTO aliment_reference (name, energie_kcal100g) VALUES ('{$nomEchappe}', {$energieFormatee})"
            );

            $count++;
        }

        fclose($handle);

        echo "Import terminé : {$count} aliments insérés.\n";
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DELETE FROM aliment_reference');
    }
}