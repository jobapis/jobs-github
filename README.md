# Github Jobs Client

[![Latest Version](https://img.shields.io/github/release/JobBrander/jobs-github.svg?style=flat-square)](https://github.com/JobBrander/jobs-github/releases)
[![Software License](https://img.shields.io/badge/license-APACHE%202.0-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/JobBrander/jobs-github/master.svg?style=flat-square&1)](https://travis-ci.org/JobBrander/jobs-github)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/JobBrander/jobs-github.svg?style=flat-square)](https://scrutinizer-ci.com/g/JobBrander/jobs-github/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/JobBrander/jobs-github.svg?style=flat-square)](https://scrutinizer-ci.com/g/JobBrander/jobs-github)
[![Total Downloads](https://img.shields.io/packagist/dt/jobbrander/jobs-github.svg?style=flat-square)](https://packagist.org/packages/jobbrander/jobs-github)

This package provides [Github Jobs API](https://jobs.github.com/api)
support for the JobBrander's [Jobs Client](https://github.com/JobBrander/jobs-common).

## Installation

To install, use composer:

```
composer require jobbrander/jobs-github
```

## Usage

Usage is the same as Job Branders's Jobs Client, using `\JobBrander\Jobs\Client\Provider\Github` as the provider.

```php
$client = new JobBrander\Jobs\Client\Provider\Github();

$jobs = $client->setKeyword('designer') // A search term, such as "ruby" or "java".
    ->setLattitude(41.8369)      // A specific latitude. If used, you must also send long and must not send location.
    ->setLongitude(87.6847)      // A specific longitude. If used, you must also send lat and must not send location.
    ->setLocation('Chicago, IL') // A city name, zip code, or other location search term.
    ->setFullTime(true)          // If you want to limit results to full time positions set this parameter to 'true'.
    ->setPage(2)                 // Pagination starts by default at 0. 50 results per page.
    ->getJobs();
```

The `getJobs` method will return a [Collection](https://github.com/JobBrander/jobs-common/blob/master/src/Collection.php) of [Job](https://github.com/JobBrander/jobs-common/blob/master/src/Job.php) objects.

## Testing

``` bash
$ ./vendor/bin/phpunit
```

## Contributing

Please see [CONTRIBUTING](https://github.com/jobbrander/jobs-github/blob/master/CONTRIBUTING.md) for details.


## Credits

- [Steven Maguire](https://github.com/stevenmaguire)
- [All Contributors](https://github.com/jobbrander/jobs-github/contributors)


## License

The Apache 2.0. Please see [License File](https://github.com/jobbrander/jobs-github/blob/master/LICENSE) for more information.
