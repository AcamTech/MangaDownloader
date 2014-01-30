Manga Downloader
================

* [English Instructions](#en)
* [Instrucciones en Español](#es)

<a name="en"></a>English Instructions
--------------------
Took my time to write a small program to download manga for me from various sources.

It's written in PHP, I know. I'll work on porting it to another better suited language, but I like PHP. So that's that.

Currently only downloads specific chapters.


### Current sources
* Mangareader.net
* Mangabird.com
* Onemanga.me
* Batoto.net
* Submanga.com


### To do
1. Make it so that a whole manga can be specified instead of only a chapter.
2. Add support for PHP Zip library.


### How to use
It's simple. Download or clone the program/repository.

You can run it from your shell using the script.php file:
```shell
$> php script.php <url>
```

Or you can use it with other systems/frameworks. The code follows PSR-4 standards.
```php
new MangaDownloader\MangaDownloader('<url>');
```

Download process will start automatically. Once finished the program will try to zip it running the exec() function. I used this because at this moment I do not have the PHP Zip library installed and I've been putting it off becuase I have to recompile PHP. But I will add it in the future (I know exec() isn't the best of options, but I made this to run it in my virtual machine server).

Make sure you have zip installed in your Linux system (yes, no Windows for now, though I haven't tested it any of the *AMP systems).


<a name="es"></a>Instrucciones en Español
------------------------
Tome un poco de mi tiempo para escribir este programa para descargar mangas desde varios sitios.

Sé que está escrito en PHP. Luego lo portaré a otro lenguaje más adecuado, pero me gusta PHP. Nada más.

Actualmente solo se pueden descargar capítulos individuales.


### Sitios soportados
* Mangareader.net
* Mangabird.com
* Onemanga.me


### Por hacer
1. Hacer que se puedan descargar mangas completos en vez de capítulos individuales.
2. Añadir soporte para la librería de Zip de PHP.


### Como se usa
Es sencillo. Descarga o clona el programa/repositorio.

Puedes correrlo desde la línea de comando utilizando el archivo script.php:
```shell
$> php script.php <direccion>
```

O lo puedes utilizar con otros sistemas/frameworks. El código sigue el estándar de PSR-4.
```php
new MangaDownloader\MangaDownloader('<direccion>');
```

El proceso de descarga iniciará de manera automática. Una vez terminado el programa intentará comprimirlo (zip) corriendo la función exec(). Utilicé esto porque al momento no tenía la librería Zip de PHP instalada y he estado dejándolo porque tendría que recompilar PHP. Pero lo añadiré en el futuro (sé que exec() no es la mejor de las opciones, pero hice esto para correrlo en un servidor en mi máquina virtual.

Asegúrense de haber instalado la libería de zip para su sistema de Linux (sí, no es para Windows por ahora, aunque no lo he probado en los ambientes *AMP que existen).
