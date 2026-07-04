<?php

$dir = new RecursiveDirectoryIterator(__DIR__ . '/src');
$iterator = new RecursiveIteratorIterator($dir);

$replacements = [
    'Illuminate\Mail\Mailable' => 'JarirAhmed\AuthMicroservice\Mailer',
    'Illuminate\Http\Request' => 'JarirAhmed\AuthMicroservice\Http\Request',
    'Illuminate\Cache\RateLimiter' => 'JarirAhmed\AuthMicroservice\RateLimiter',
    'Illuminate\Routing\Controller' => 'JarirAhmed\AuthMicroservice\Http\Controller',
    'Illuminate\Support\Facades\Hash' => 'JarirAhmed\AuthMicroservice\Support\Hash',
    'Illuminate\Support\Facades\Auth' => 'JarirAhmed\AuthMicroservice\Support\Auth',
    'Illuminate\Support\Facades\Mail' => 'JarirAhmed\AuthMicroservice\Mailer',
    'extends Mailable' => 'extends \JarirAhmed\AuthMicroservice\Mailer'
];

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        $original = $content;
        
        foreach ($replacements as $search => $replace) {
            $content = str_replace($search, $replace, $content);
        }
        
        if ($content !== $original) {
            file_put_contents($file->getPathname(), $content);
            echo "Updated: " . $file->getPathname() . "\n";
        }
    }
}
echo "Refactoring completed.\n";
