
# UE L312 Projet individuel

Serghei

Trois exercices PHP sur les interfaces, les iterateurs et le cycle de vie d'un objet.

## Demarrage dans VS Code

Ouvrir ce dossier dans VS Code. Dans le terminal PowerShell integre, verifier PHP puis installer les dependances.

    php -v
    composer --version
    composer install
    composer check-platform-reqs

Le projet utilise PHP 8.2 ou superieur avec PHPUnit 11. Le fichier composer.lock fixe les versions installees.

## Verifications

    composer lint
    composer test

## Exercices

Depuis le terminal integre de VS Code, lancer le point d'entree pour voir les trois exercices. Chaque argument rejoue ensuite les etapes d'un seul exercice. PHP suffit.

    php public/index.php
    php public/index.php fibonacci
    php public/index.php defer
    php public/index.php bitarray

Pour voir le meme apercu dans le navigateur, lancer `php -S 127.0.0.1:8312 -t public`, puis ouvrir `http://127.0.0.1:8312`.

- FibonacciSequence implemente [Iterator](https://www.php.net/manual/fr/class.iterator.php). Il calcule les termes a la demande et partage leur cache. Le calcul s'arrete avec une exception si un resultat depasse la capacite d'un entier PHP.
- Defer garde les appels et leurs arguments. Le [destructeur](https://www.php.net/manual/fr/language.oop5.decon.php) les execute du dernier ajoute au premier quand la derniere reference disparait. Si un appel echoue, les suivants s'executent avant que la premiere erreur soit relayee.
- BitArray range les bits par blocs et utilise [ArrayAccess](https://www.php.net/manual/fr/class.arrayaccess.php) pour les crochets. L'indice 0 correspond au bit de droite. Une case effacee vaut 0 et la longueur logique est conservee. Pour un entier negatif, fromInt reprend la representation binaire de PHP sur la largeur d'un entier.

## Apercu en ligne

[Voir les resultats](https://solips1sm.github.io/UE_L312_S1/). GitHub Pages sert le HTML produit par le projet PHP.

## Origine

[Boilerplate de Thomas Teixeira](https://codeberg.org/cvtic-312/cvtic-312-boilerplate-solo), CvTIC, Universite de Limoges.
