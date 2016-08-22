{
    "name": "n2n/page",
    "type": "n2n-module",
    "description": "rocket cmf",
    "keywords": ["cms","cmf","n2n],
    "homepage": "https://n2n.rocks/",
    "license": "GNU LGPL",
    "authors": [
        {
            "name": "Andreas von Burg",
            "email": "a@von-burg.net",
            "homepage": "http://www.von-burg.net",
            "role": "Developer"
        },
        {
            "name": "Bert Hofmänner",
            "email": "hofmaenner@hnm.ch",
            "role": "Developer"
        },
        {
            "name": "Thomas Günther",
            "email": "guenther@hnm.ch",
            "role": "Developer"
        }
    ],
    "require": {
        "php": ">=7.0",
        "n2n/n2n-module-composer-plugin": "master-dev",
        "n2n/n2n": "master-dev",
        "n2n/n2n-web": "master-dev",
        "n2n/rocket": "master-dev"
    },
    "autoload": {
        "psr-4": {
            "rocket\\": "src/app/rocket"
        }
    }
}
