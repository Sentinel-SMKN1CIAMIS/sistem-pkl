<?php
$directory = 'c:/xampp/htdocs/sistem-pkl-v13/resources/views';

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
$regex = new RegexIterator($iterator, '/^.+\.blade\.php$/i', RecursiveRegexIterator::GET_MATCH);

foreach ($regex as $file => $object) {
    $content = file_get_contents($file);
    $lines = explode("\n", $content);
    $changed = false;
    
    foreach ($lines as &$line) {
        if (strpos($line, 'bg-blue-600') !== false && strpos($line, 'text-slate-900 dark:text-white') !== false) {
            $line = str_replace('text-slate-900 dark:text-white', 'text-white', $line);
            $changed = true;
        }
    }
    
    if ($changed) {
        file_put_contents($file, implode("\n", $lines));
        echo "Updated: $file\n";
    }
}
echo "Done.\n";
