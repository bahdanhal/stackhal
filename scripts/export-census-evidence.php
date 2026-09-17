<?php

declare(strict_types=1);

// Export recorded observations, not registry responses or repository identities.
// Use a consistent SQLite backup as input; never an active database with this URI.
if ($argc !== 3) {
    throw new InvalidArgumentException('Usage: php export-census-evidence.php snapshot.sqlite output.sqlite');
}
if (file_exists($argv[2])) {
    throw new RuntimeException('Refusing to overwrite an existing evidence export.');
}
$sourcePath = realpath($argv[1]);
if ($sourcePath === false) {
    throw new RuntimeException('Source snapshot does not exist.');
}
$source = new PDO('sqlite:file:' . $sourcePath . '?immutable=1');
$source->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$output = new PDO('sqlite:' . $argv[2]);
$output->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$output->beginTransaction();
foreach (['repos', 'dependencies', 'scanned_slices'] as $table) {
    $schema = $source->prepare('SELECT sql FROM sqlite_master WHERE type = ? AND name = ?');
    $schema->execute(['table', $table]);
    $output->exec((string) $schema->fetchColumn());
    $rows = $source->query('SELECT * FROM ' . $table);
    $insert = null;
    while ($row = $rows->fetch(PDO::FETCH_ASSOC)) {
        if (isset($row['repo_name'])) {
            $row['repo_name'] = hash('sha256', $row['repo_name']);
        }
        if ($insert === null) {
            $insert = $output->prepare('INSERT INTO ' . $table . ' (' . implode(', ', array_keys($row)) . ') VALUES (' . implode(', ', array_fill(0, count($row), '?')) . ')');
        }
        $insert->execute(array_values($row));
    }
}
$output->commit();
$output->exec('VACUUM');
$output = null;
$bytes = file_get_contents($argv[2]);
if ($bytes === false || file_put_contents($argv[2] . '.gz', gzencode($bytes, 9)) === false) {
    throw new RuntimeException('Could not compress evidence export.');
}
echo 'Evidence export SHA-256: ' . hash('sha256', $bytes) . PHP_EOL;
