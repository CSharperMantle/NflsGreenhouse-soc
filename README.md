# Greenhouse Project of Ningbo Foreign Language School
## Introduction
This project is developed to be an automatic greenhouse controller.
## Installation
### Requirements
* Required `PHP7+`
* Required `session.auto_start=1`
* Required settings in Apache: `ErrorDocument 404 .../NflsWatering/website/html/pages-404.html`
## API
APIs are provided in RESTful or SOAP styles.
```
website |--api
        |   |--v1.0 = SOAP APIs
        |   |   |--upload.php
        |   |--v1.1 = RESTful APIs
        |   |   |--upload.php
        |   |--v2.0 = RESTful APIs, master-slave seperated
        |   |   |--upload.php
        |   |   |--fetch.php
        |   |--internal = Internal APIs
```
## Acknowledgement
### Libs in use
* `HttpParser` in C by Joyent Inc. and other Node contributors
* `LiquidCrystal_I2C` in Arduino C++ by Frank de Brabander
* `PacketParser` in C++ by Mantle Jonse and iRed_K
* `ProtoThreads` in C by Adam Dunkels
* `TinyXML` in C++ by Lee Thomason