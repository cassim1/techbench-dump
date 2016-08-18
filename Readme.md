Info
----
This script obtains links from Microsoft's API and then writes them to file.<br>
Currently it is based on cUrl and BusyBox.

techbench.cmd - Obtains links from API, and then writes them to formatted HTML file<br>
techbench_md.cmd - Obtains links from API, and then writes them to formatted Markdown file (GitHub paste format)

Usage
-----
Simply run desired script, it will generate everything automatically.<br>
Generation takes about 15 minutes.

Command line usage:
```
<script.cmd> [first_id] [last_id]
```

Example command to create HTML file with products from range between 242 and 247:
```
techbench.cmd 242 247
```

Credits
-------
WzorNET - finding out that TechBench contains more than Windows 10.<br>
Ron Yorston - BusyBox port for Windows. https://frippery.org/busybox/<br>
Stefan Kanthak - cUrl binaries for Windows. https://skanthak.homepage.t-online.de/curl.html
