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

        // Read all html files from /resources/views/template/src directory
        $this->processDirectory(resource_path('views/template/src'));

        $this->info('Template built successfully!');
    }

    private function processDirectory(string $path): void
    {
        $base_template_folder = resource_path('views/template/src');
        $current_directory = str_replace($base_template_folder, '', $path);

        // Check target directory
        if (!empty($current_directory) and $current_directory !== '/') {
            $target_directory = resource_path('views/template/dist') . $current_directory;
        } else {
            $target_directory = resource_path('views/template/dist');
        }

        // Check if target directory exists and if so, remove it
        if (is_dir($target_directory)) {
            // Remove the directory
            $this->info('Removing the ' . $target_directory . ' directory...');
            File::deleteDirectory($target_directory);
        }

        // Create the target directory
        $this->info('Creating the ' . $target_directory . ' directory...');
        mkdir($target_directory);

        $target_directory .= '/';

        $files = glob($path . '/*.html');

        // Loop through all files and copy them as .blade.php file to target directory
        foreach ($files as $file) {
            $filename = pathinfo($file, PATHINFO_FILENAME);
            $this->info('Copying ' . $filename . '...');

            $source = file_get_contents($file);
            $target = $this->convertHtmlToBlade($source);


            file_put_contents($target_directory . $filename . '.blade.php', $target);
        }

        $asset_folders = ['assets', 'css', 'js', 'img', 'vendor', 'scss'];

        // Copy assets or process subdirectories

        // Read all directories from the current directory
        $directories = glob($path . '/*', GLOB_ONLYDIR);
        foreach($directories as $directory) {
            // Get the directory name
            $directoryName = pathinfo($directory, PATHINFO_BASENAME);

            // Check if the directory is an asset folder
            if (in_array($directoryName, $asset_folders)) {
                // Copy the asset folder to the public directory
                $sourcePath = $directory;
                $destinationPath = public_path(str_replace($base_template_folder . '/', '', $directory));

                File::copyDirectory($sourcePath, $destinationPath);
                $this->info('The ' . $directoryName . ' folder was successfully copied to public/' . $directoryName . '!');
            } else {
                $this->processDirectory($directory);
            }
        }
    }

    private function convertHtmlToBlade(string $source): string
    {
        $general_cotent_pattern = '[\w\s\-\.\/·,!&]+';
        $single_name_pattern = '[\w\-\.\/\_]+';

        // Convert relative paths to absolute paths
        $source = str_replace('href="./', 'href="/', $source);
        $source = str_replace('src="./', 'src="/', $source);

        // Create language keys for all content tags
        $source = preg_replace('/placeholder="('.$general_cotent_pattern.')"/', 'placeholder="{{ __(\'$1\') }}"', $source);

        $content_tags = ['a', 'button', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'span', 'div', 'p', 'option', 'li', 'label'];
        foreach($content_tags as $tag) {
            preg_match_all('/<' . $tag . '[^>]*>('.$general_cotent_pattern.')<\/' . $tag . '>/', $source, $matches);
            foreach( $matches[0] as $i => $match ) {
                // Remove spaces, new lines and tabs
                $literal = $matches[1][$i];
                $literal = str_replace("\n", '', $literal);
                $literal = str_replace("\r", '', $literal);
                $literal = str_replace("\t", '', $literal);
                $literal = trim($literal);

                // Replace all ocurrences, only for content tags
                $replacement = str_replace('>'.$matches[1][$i].'<', '>{{ __(\'' . $literal . '\') }}<', $match);

                // Replace again the replaced text into the source.
                $source = str_replace($match, $replacement, $source);
            }
        }

        /* Assets */
        $assets_href = ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'ico', 'webp', 'woff', 'woff2', 'ttf', 'eot', 'otf'];
        foreach($assets_href as $asset) {
            $source = preg_replace('/href="('.$single_name_pattern.')\.' . $asset . '"/', 'href="{{ asset(\'$1.' . $asset . '\') }}"', $source);
        }
        $source = preg_replace('/src="('.$single_name_pattern.')"/', 'src="{{ asset(\'$1\') }}"', $source);

        /* Routes */
        // Replaces <a href="X.html"> to <a href="{{ route('template', ['page' => 'X']) }}">
        // TODO: Check if is need to replace P/D/X.html to P.D.X in the route
        $source = preg_replace('/href="('.$single_name_pattern.')\.html"/', 'href="{{ route(\'template.page\', [\'page\' => \'$1\']) }}"', $source);

        // Replaces url(PATHTO/X) to url({{ asset('PATHTO/X) }}"
        $source = preg_replace('/url\(('.$single_name_pattern.')\)/', 'url({{ asset(\'$1\') }})', $source);

        // Fix some incorrect blade syntax
        $source = str_replace('{{ asset(\'.\') }}', '#', $source);
        $source = str_replace('{{ asset(\'/\') }}', '{{ route(\'template\']) }}', $source);

        return $source;
    }
}
