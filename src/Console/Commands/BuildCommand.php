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

        // Copiar a pasta /resources/views/templates/src/assets para /public/assets
        $asset_folders = ['assets', 'css', 'js', 'img', 'vendor', 'scss'];
        foreach($asset_folders as $asset_folder) {
            $sourcePath = resource_path('views/template/src/' . $asset_folder);
            $destinationPath = public_path($asset_folder);

            if (File::exists($sourcePath)) {
                File::copyDirectory($sourcePath, $destinationPath);
                $this->info('A pasta ' . $asset_folder . ' foi copiada com sucesso para public/assets!');
            } else {
                $this->error('A pasta source /resources/views/template/src/' . $asset_folder . ' não existe.');
            }
        }

        $this->info('Template built successfully!');
    }

    private function convertHtmlToBlade(string $source): string
    {
        // Create language keys for all content tags
        $source = preg_replace('/placeholder="([\w\s\-·,!]+)"/', 'placeholder="{{ __(\'$1\') }}"', $source);

        $content_tags = ['a', 'button', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'span', 'div', 'p'];
        foreach($content_tags as $tag) {
            preg_match_all('/<' . $tag . '[^>]*>([\w\s\-·,!]+)<\/' . $tag . '>/', $source, $matches);
            foreach( $matches[0] as $i => $match ) {
                // Remove spaces, new lines and tabs
                $literal = $matches[1][$i];
                $literal = str_replace("\n", '', $literal);
                $literal = str_replace("\t", '', $literal);
                $literal = trim($literal);

                // Replace all ocurrences, only for content tags
                $replacement = str_replace('>'.$matches[1][$i].'<', '>{{ __(\'' . $literal . '\') }}<', $match);

                // Replace again the replaced text into the source.
                $source = str_replace($match, $replacement, $source);
            }
        }

        // Replaces <a href="X.html"> to <a href="{{ route('template', ['page' => 'X']) }}">
        $source = preg_replace('/href="(.+?)\.html"/', 'href="{{ route(\'template.page\', [\'page\' => \'$1\']) }}"', $source);

        // Replaces src="PATHTO/X" to src="{{ asset('PATHTO/X') }}"
        $source = preg_replace('/src="(.+?)"/', 'src="{{ asset(\'$1\') }}"', $source);

        // Replaces href="PATHTO/X.css" to href="{{ asset('PATHTO/X.css') }}"
        $source = preg_replace('/href="(.+?)\.css"/', 'href="{{ asset(\'$1.css\') }}"', $source);

        // Replaces src="PATHTO/X.js" to src="{{ asset('PATHTO/X.js') }}"
        $source = preg_replace('/src="(.+?)\.js"/', 'src="{{ asset(\'$1.js\') }}"', $source);

        // Replaces url(PATHTO/X) to url({{ asset('PATHTO/X) }}"
        $source = preg_replace('/url\((.+?)\)/', 'url({{ asset(\'$1\') }})', $source);

        // Fix some incorrect blade syntax
        $source = str_replace('{{ asset(\'.\') }}', '#', $source);
        $source = str_replace('{{ asset(\'/\') }}', '{{ route(\'template.page\', [\'page\' => \'index\']) }}', $source);

        return $source;
    }
}
