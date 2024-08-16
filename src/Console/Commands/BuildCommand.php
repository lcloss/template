<?php

namespace Lcloss\Template\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class BuildCommand extends Command
{
    /**
     * @var string The name and signature of the console command.
     */
    protected $signature = 'template:build';

    /**
     * @var string The console command description.
     */
    protected $description = 'Build the template';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Building the template...');

        // Check if /resources/views/template/src directory exists
        if (!is_dir(resource_path('views/template/src'))) {
            $this->error('The /resources/views/template/src directory does not exist.');
            return;
        }

        // Check if /resources/views/template/dist directory exists
        if (is_dir(resource_path('views/template/dist'))) {
            // Remove the directory
            $this->info('Removing the /resources/views/template/dist directory...');
            File::deleteDirectory(resource_path('views/template/dist'));
        }

        // Create the /resources/views/template/dist directory
        $this->info('Creating the /resources/views/template/dist directory...');
        mkdir(resource_path('views/template/dist'));

        // Read all html files from /resources/views/template/src directory
        $files = glob(resource_path('views/template/src/*.html'));

        // Loop through all files and copy them as .blade.php file to /resources/views/template/dist directory
        foreach ($files as $file) {
            $filename = pathinfo($file, PATHINFO_FILENAME);
            $this->info('Copying ' . $filename . '...');

            $source = file_get_contents($file);
            $target = $this->convertHtmlToBlade($source);

            file_put_contents(resource_path('views/template/dist/' . $filename . '.blade.php'), $target);
        }

        // Caminho de origem e destino para a cópia dos arquivos
        $sourcePath = resource_path('views/template/src/assets');
        $destinationPath = public_path('assets');

        // Copiar a pasta /resources/views/templates/src/assets para /public/assets
        if (File::exists($sourcePath)) {
            File::copyDirectory($sourcePath, $destinationPath);
            $this->info('A pasta assets foi copiada com sucesso para public/assets!');
        } else {
            $this->error('A pasta source /resources/views/template/src/assets não existe.');
        }

        $this->info('Template built successfully!');
    }

    private function convertHtmlToBlade(string $source): string
    {
        // Replaces <a href="X.html"> to <a href="{{ route('template', ['page' => 'X']) }}">
        $source = preg_replace('/href="(.+?)\.html"/', 'href="{{ route(\'template.page\', [\'page\' => \'$1\']) }}"', $source);

        // Replaces src="PATHTO/X.jpg" to src="{{ asset('PATHTO/X.jpg') }}"
        $source = preg_replace('/src="(.+?)\.jpg"/', 'src="{{ asset(\'$1.jpg\') }}"', $source);

        // Replaces src="PATHTO/X.png" to src="{{ asset('PATHTO/X.png') }}"
        $source = preg_replace('/src="(.+?)\.png"/', 'src="{{ asset(\'$1.png\') }}"', $source);

        // Replaces href="PATHTO/X.css" to href="{{ asset('PATHTO/X.css') }}"
        $source = preg_replace('/href="(.+?)\.css"/', 'href="{{ asset(\'$1.css\') }}"', $source);

        // Replaces src="PATHTO/X.js" to src="{{ asset('PATHTO/X.js') }}"
        $source = preg_replace('/src="(.+?)\.js"/', 'src="{{ asset(\'$1.js\') }}"', $source);

        return $source;
    }
}
