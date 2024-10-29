<div align="center">
   
   # Dataflow
   
   `Dataflow` is a cross-platform web based <b>file manager</b> with looks
   
   [![PHP](https://img.shields.io/badge/Language-PHP-c7b9ed?logo=php&logoColor=fff&style=for-the-badge)](https://en.wikipedia.org/)
   [![FRAMEWORK](https://img.shields.io/badge/Framework-Symfony-000000?logo=symfony&logoColor=fff&style=for-the-badge)](https://en.wikipedia.org/wiki/PHP)
    
</div>

<br>

## Idea
Two factors played the role: 1 - We've never found a good looking file manager on Linux desktops to begin with, we have a plenty of [Midnight Commander](https://github.com/MidnightCommander/mc), [Thunar](https://github.com/xfce-mirror/thunar), [Dolphin](https://github.com/KDE/dolphin), [Krusader](https://github.com/KDE/krusader) and some others but they are either not cross-platform or simply lack the style and sometimes functionality like searching text <u>inside</u> of files. 2 - Since Windows 11 24H2 a new *"feature"* that is actually the most insane security breach of the decade (along side CrowdStrike and Riot Vanguard) **[Windows Recall](https://support.microsoft.com/en-us/windows/retrace-your-steps-with-recall-aa03f8a0-a78b-4b3e-b0a1-2eb8ac48701c)**, yes that thing that's doing screenshots of your desktop.

This feature is now mandatory for the [explorer.exe](https://en.wikipedia.org/wiki/File_Explorer) to work which essentially not only the file manager but also the holder of your desktop. What Microsoft did is simple - Recall is now a dependency of the most vital process and if you ever try to remove this feature for it to never magically turn on in the background while you sleep - you will lose your [explorer.exe](https://en.wikipedia.org/wiki/File_Explorer) therefore your file manager.

So we decided to make something of our own craft to work with files on both operating systems inside of any browser of your taste on the most popular web oriented language [PHP](https://en.wikipedia.org/wiki/PHP) with not less known framework for it [Symfony](https://symfony.com/what-is-symfony) and make it look good using [Tailwind](https://tailwindcss.com/).
