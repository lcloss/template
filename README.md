# Template Package

## Description

Do you have a template written in HTML and want to convert it to Blade, to use in a Laravel project?
Then this package is for you. Its use is simple, and what you want is just the beginning to start your new Laravel project, from an HTML template.

## Installation

You can install the package via composer:

```bash
composer require lcloss/template
```

## Usage

1. To convert your HTML files to Blade, start by creating the `/resources/views/templates/src` directory.
2. Copy your HTML files into the `/src` folder above.
3. If the template comes with `assets` directory, also copy the `assets` folder into `/src`.
4. Then, run the command:
```bash
php:build artisan template:build
```
5. The package will create the `/resources/views/templates/dist` folder with the converted files.
6. It will also create routes to view your template.

Open the browser at: http://seu-projeto.test/template
This route points to `index.blade.php`.

If there is another file as the root, such as `home.html`, then open the browser at:
http://seu-projeto.test/template/home

## Credits

- [Lucas Closs](lcloss @ github)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security

If you discover any security-related issues, please email
instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Code of Conduct

Please see [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Testing

