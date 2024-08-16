# Pacote Template

## Outras línguas

- [English](README.md)
- [Português](README-pt.md)

## Descrição

Você tem um template escrito em HTML e deseja convertê-lo para Blade, para usar em um projeto Laravel?
Então este pacote é para você. Seu uso é simples, e o que você deseja é apenas o começo para iniciar seu novo projeto Laravel, a partir de um template HTML.

## Instalação

Você pode instalar o pacote via composer:

```bash
composer require lcloss/template
```

## Uso

1. Para converter seus ficheiros HTML para Blade, comece criando o diretório `/resources/views/templates/src`.
2. Copie seus ficheiros HTML para a pasta `/src` acima.
3. Se o template vier com a pasta `assets`, copie também a pasta `assets` para `/src`.
4. Em seguida, execute o comando:
```bash
php:build artisan template:build
```
5. O pacote criará a pasta `/resources/views/templates/dist` com os ficheiros convertidos.
6. Ele também criará rotas para visualizar seu template.

Abra o navegador em: http://seu-projeto.test/template
Esta rota aponta para `index.blade.php`.

Se houver outro ficheiro como raiz, como `home.html`, então abra o navegador em:
http://seu-projeto.test/template/home

## Créditos

- [Luciano Closs](lcloss @ github)

## Licença

A Licença MIT (MIT). Consulte o [Arquivo de Licença](LICENSE-pt.md) para obter mais informações.

## Changelog

Por favor, veja [CHANGELOG](CHANGELOG-pt.md) para mais informações sobre o que mudou recentemente.

## Segurança

Se você descobrir quaisquer problemas relacionados à segurança, envie um e-mail
em vez de usar o rastreador de problemas.

## Contribuindo

Por favor, veja [CONTRIBUTING](CONTRIBUTING-pt.md) para detalhes.

## Código de Conduta

Por favor, veja [CODE_OF_CONDUCT](CODE_OF_CONDUCT-pt.md) para detalhes.

## Testing

Nenhum teste foi escrito, pois é apenas para converter ficheiros.
Para uma próxima versão, provavelmente irei adicionar a configuração de pasta via `.env`, para que você possa criar testes.
Por enquanto, queremos algo bem simples.
