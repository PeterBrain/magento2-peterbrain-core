# Magento 2 Module - PeterBrain Core

Package name: `peterbrain/magento2-peterbrain-core`

- [Magento 2 Module - PeterBrain Core](#magento-2-module---peterbrain-core)
  - [Main Functionalities](#main-functionalities)
  - [Installation](#installation)
    - [Method 1: Composer (recommended)](#method-1-composer-recommended)
    - [Method 2: Zip file (not recommended)](#method-2-zip-file-not-recommended)
    - [Enable & deploy](#enable--deploy)

## Main Functionalities

PeterBrain Extension core module - requirement for PeterBrain Extensions

- View system information
- View module information
- Run update check (manual/auto)
- Notifications
- PeterBrain Menu

View vital information about your system:
![View vital information about your system.](https://github.com/peterbrain/magento2-peterbrain-core/blob/media/core_system-information.jpg?raw=true)
Admin configuration:
![Admin configuration](https://github.com/peterbrain/magento2-peterbrain-core/blob/media/core_configuration.jpg?raw=true)

## Installation

### Method 1: Composer (recommended)

```bash
composer require peterbrain/magento2-peterbrain-core
```

### Method 2: Zip file (not recommended)

- Unzip the zip file in `app/code/PeterBrain`

### Enable & deploy

```bash
php bin/magento module:enable PeterBrain_Core
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
php bin/magento cache:flush
```
