<?php declare(strict_types=1);

require __DIR__ . '/../src/FibonacciSequence.php';
require __DIR__ . '/../src/Defer.php';
require __DIR__ . '/../src/BitArray.php';

if (PHP_SAPI === 'cli' && isset($argv[1])) {
    $examples = [
        'fibonacci' => 'fibonacci.php',
        'defer' => 'defer.php',
        'bitarray' => 'bitarray.php',
    ];
    if (!isset($examples[$argv[1]])) {
        fwrite(STDERR, "usage: php public/index.php [fibonacci|defer|bitarray]\n");
        exit(1);
    }

    require __DIR__ . '/../examples/' . $examples[$argv[1]];
    return;
}

$lalangiana = iterator_to_array(FibonacciSequence::first(10), false);
$maniana = [];

function montrerDefer(array &$maniana): void {
    Defer::init($galanginho);
    $ajouter = function (string $ligne) use (&$maniana): void {
        $maniana[] = $ligne;
    };
    $galanginho($ajouter, 'premier ajoute');
    $galanginho($ajouter, 'dernier ajoute');
    // both callbacks wait until this function returns
    $maniana[] = 'dans la fonction';
}

montrerDefer($maniana);
$muniana = BitArray::fromString('0b1010010111110000');
$esketianne = [];
foreach ($muniana->getSlicesIterator(4) as $variablianne) {
    $esketianne[] = (string) $variablianne;
}

if (PHP_SAPI === 'cli') {
    echo 'FibonacciSequence: ', implode(', ', $lalangiana), PHP_EOL;
    echo 'Defer: ', implode(' -> ', $maniana), PHP_EOL;
    echo 'BitArray: ', $muniana, ' -> ', implode(', ', $esketianne), PHP_EOL;
    return;
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>UE L312, Exercices PHP</title>
  <style>
    body { font-family: "Century Gothic", system-ui, sans-serif; line-height: 1.55; color: #182238; margin: 0; }
    main { max-width: 58rem; margin: 3rem auto; padding: 0 1.25rem; }
    h1 { color: #1f3864; margin-bottom: .5rem; }
    h2 { color: #775406; margin: 0; font-size: 1.1rem; }
    section { padding: 1rem 1.25rem; margin: 1rem 0; border-left: 3px solid #1f3864; background: #eef4fa; }
    p { margin: .5rem 0; }
    code { font-size: 1rem; overflow-wrap: anywhere; }
    .intro { margin-bottom: 1.5rem; }
  </style>
</head>
<body>
<main>
  <h1>Exercices PHP</h1>
  <p class="intro">R&eacute;sultats du projet individuel UE L312</p>
  <section>
    <h2>FibonacciSequence</h2>
    <p>Dix termes, calcul&eacute;s au fil du parcours.</p>
    <code><?= implode(', ', $lalangiana) ?></code>
  </section>
  <section>
    <h2>Defer</h2>
    <p>Ordre d&rsquo;ex&eacute;cution observ&eacute;.</p>
    <ol>
      <?php foreach ($maniana as $ligne): ?>
        <li><?= htmlspecialchars($ligne, ENT_QUOTES, 'UTF-8') ?></li>
      <?php endforeach; ?>
    </ol>
  </section>
  <section>
    <h2>BitArray</h2>
    <p><code><?= $muniana ?></code> <?= count($muniana) ?> bits</p>
    <p>Tranches de quatre bits, en commen&ccedil;ant par les bits de poids faible.</p>
    <code><?= implode(', ', $esketianne) ?></code>
  </section>
</main>
</body>
</html>
