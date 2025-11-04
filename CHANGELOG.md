# Changelog

## 1.0.0 (2025-11-04)


### Features

* **api:** ✨ add new UGC and Media API controllers ([#17](https://github.com/webmappsrl/carg/issues/17)) ([15abeb9](https://github.com/webmappsrl/carg/commit/15abeb900893bcc50fbbd5d3be0d401c2d1158a7))
* **console:** ✨ add ProcessAllSheetsZip command oc:6493 ([#16](https://github.com/webmappsrl/carg/issues/16)) ([ba13348](https://github.com/webmappsrl/carg/commit/ba13348fb94ade807dfb7b97d938fbd2e3f12125))
* **models:** ✨ add 'properties' attribute casting to User model ([f1437fb](https://github.com/webmappsrl/carg/commit/f1437fb01615d49fb85e0b0dbd2277503d0f70ba))
* **observers:** add AppFeatureCollectionConfigObserver to handle App saved event OC:5611 ([7b40860](https://github.com/webmappsrl/carg/commit/7b4086077862dceac9c4ccefb72a9036eeae9a9e))
* **server:** ✨ serve files from /storage directly without Laravel ([45e9e71](https://github.com/webmappsrl/carg/commit/45e9e7142a5309c7943e33c18d1c2f579d509017))


### Bug Fixes

* **command:** 🐛 update stream writing method in CargZipTilesS3Copy command ([354a315](https://github.com/webmappsrl/carg/commit/354a315918ae08f1d4be6704cbe51cd211f35e85))
* **dev-deploy:** 🐛 update docker exec command for development deployment ([cb2f578](https://github.com/webmappsrl/carg/commit/cb2f5784975fb3df9b3c846521908018118d2b9d))
* **ExampleTest:** update test to assert redirect to login ([dbd0ab6](https://github.com/webmappsrl/carg/commit/dbd0ab602578b3c6b9e19a5a58861d59eb33b83b))
* **filesystems:** 🐛 update blankmap storage configuration to SFTP ([47e35ef](https://github.com/webmappsrl/carg/commit/47e35ef9e8a6aa0c4a215550906ee2655719b217))
* **nova:** 🐛 comment out email for user access control ([092a5d2](https://github.com/webmappsrl/carg/commit/092a5d20cd1f79401cedbccc146bd6d4338cdf18))
* typo ([59c85a7](https://github.com/webmappsrl/carg/commit/59c85a779f3d830fecce3d7a8bc92583aa89279a))


### Miscellaneous Chores

* **AppServiceProvider:** 🔧 add App model import ([d250876](https://github.com/webmappsrl/carg/commit/d2508769f7ddca69ee1d4416d0bbbf0bc3baaa7c))
* **filesystems:** 🔧 change wmfe storage configuration from S3 to local ([e0830d2](https://github.com/webmappsrl/carg/commit/e0830d2df9f0ae6fb10c5327a1c5bacff5ac2df9))

## 1.0.0 (2024-04-03)


### Features

* 1st version apis ([bd7f186](https://github.com/webmappsrl/carg/commit/bd7f1868a41625530bda241906adc5632c49bd47))
* Add migrations, seeders, and models for ConfFeatureCollection and FeatureCollection ([1b23b9e](https://github.com/webmappsrl/carg/commit/1b23b9e0a92b3699e2aa5b400c8ef640682dca48))
* added cs fixer, release-please and run-tests workflows ([3f13f57](https://github.com/webmappsrl/carg/commit/3f13f5763167308853650b19d201b063f0b31647))
* added cs-fixer config file ([e0f62f8](https://github.com/webmappsrl/carg/commit/e0f62f8409a83c37e84acd2883e97e041f7b869f))
* added dev-deploy and prod-deploy workflows ([308ff2d](https://github.com/webmappsrl/carg/commit/308ff2ddd961ccde56aee8bb8b4a1c89b85d85ce))
* created sheet model ([3496445](https://github.com/webmappsrl/carg/commit/3496445322b459328a638b190672279f6ab63dd6))
* implemented areas.json api ([164f379](https://github.com/webmappsrl/carg/commit/164f37993db93b82fb8320056a0a2b8af9524128))
* implemented nova gate for admin@webmapp ([d74d979](https://github.com/webmappsrl/carg/commit/d74d979cd9f0decf6b790705a1d04de408ae1cde))
* updated project environment configuration ([55e51e7](https://github.com/webmappsrl/carg/commit/55e51e7602d0c32cd47748a376cdee5b747d576e))
* updated xdebug conf ([38dcf46](https://github.com/webmappsrl/carg/commit/38dcf461852042683ebadabaca9f6bf4b6386d58))


### Miscellaneous Chores

* Refactor SheetController.php ([a9363c4](https://github.com/webmappsrl/carg/commit/a9363c4c2db28ca8ef8eeffbd3d2c10e3a78034a))
* Update .gitignore to exclude .php-cs-fixer.cache ([84bc69d](https://github.com/webmappsrl/carg/commit/84bc69d38902e06ff9601c86a08a92de2ef57a8b))
* Update FeatureCollection and Sheet ([e816e2f](https://github.com/webmappsrl/carg/commit/e816e2f0702412ed8ff4496dbc6636e4a0d34fe1))
* Update FeatureCollection.php and filesystems.php ([90bb2a5](https://github.com/webmappsrl/carg/commit/90bb2a5abf3a77adca889b4047e76308bc82f419))
* Update HasOne import in Sheet.php ([3302a32](https://github.com/webmappsrl/carg/commit/3302a323a733cefa2a10bc4d49b122b61497c667))
* Update subproject ([66476a5](https://github.com/webmappsrl/carg/commit/66476a5da377a7315eb24156d1f674c3b46d4d2a))
