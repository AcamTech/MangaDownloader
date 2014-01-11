Manga Downloader
================

Took my time to write a small program to download manga for me from various sources.

It's written in PHP, I know. I'll work on porting it to another better suited language, but I like PHP. So that's that.

Currently only downloads specific chapters.


Current Sources
---------------
* Mangareader.net
* Mangabird.com
* Onemanga.me


To Do
-----
1. Make it so that a whole manga can be specified instead of only a chapter.
2. Add support for PHP Zip library.


How to Use
----------
It's simple. Download or clone the program.

You can run it from your shell using the script.php file:
```shell
$> php script.php <url>
```

Or you can use it with other systems/frameworks. The code follows PSR-4 standards.
```php
new MangaDownloader\MangaDownloader('<url>');
```

Download process will start automatically. Once finished the program will try to zip it running the exec() function. I used this because at this moment I do not have the PHP Zip library installed and I've been putting it off becuase I have to recompile PHP. But I will add it in the future (I know exec isn't the best of options, but I made this to run it in my virtual machine server).

Make sure you have zip installed in your Linux system (yes, no Windows for now, though I haven't tested it any of the *AMP systms).