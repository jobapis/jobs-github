<?php namespace JobApis\Jobs\Client\Providers;

use DateTime;
use JobApis\Jobs\Client\Job;

class GithubProvider extends AbstractProvider
{
    /**
     * Access token
     *
     * @var string
     */
    protected $token;

    /**
     * Map of setter methods to search parameters
     *
     * @var array
     */
    protected $searchMap = [
        'setLattitude' => 'lat',
        'setLongitude' => 'long',
        'setLocation' => 'location',
        'setKeyword' => 'description',
        'setPage' => 'page',
    ];

    /**
     * Current search parameters
     *
     * @var array
     */
    protected $searchParameters = [
        'description' => null,
        'location' => null,
        'lat' => null,
        'long' => null,
        'full_time'  => null,
        'page'  => null,
    ];

    /**
     * Create new github jobs client.
     *
     * @param array $parameters
     */
    public function __construct($parameters = [])
    {
        parent::__construct($parameters);
        array_walk($parameters, [$this, 'updateQuery']);
    }

    /**
     * Magic method to handle get and set methods for properties
     *
     * @param  string $method
     * @param  array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (isset($this->searchMap[$method], $parameters[0])) {
            $this->updateQuery($parameters[0], $this->searchMap[$method]);
        }

        return parent::__call($method, $parameters);
    }

    /**
     * Returns the standardized job object.
     *
     * @param array $payload
     *
     * @return \JobBrander\Jobs\Client\Job
     */
    public function createJobObject($payload)
    {
        $job = new Job;

        $map = $this->getJobSetterMap();

        array_walk($map, function ($path, $setter) use ($payload, &$job) {
            try {
                $value = static::getValue(explode('.', $path), $payload);
                $job->$setter($value);
            } catch (\OutOfRangeException $e) {
                // do nothing
            }
        });

        $job = $this->setCityStateLocation($job);

        return $job;
    }

    /**
     * Get data format.
     *
     * @return string
     */
    public function getFormat()
    {
        return 'json';
    }

    /**
     * Retrieves array that maps job setter methods to payload keys.
     *
     * @return array
     */
    protected function getJobSetterMap()
    {
        return [
            'setCompanyName' => 'company',
            'setCompanyLogo' => 'company_logo',
            'setCompanyUrl' => 'company_url',
            'setDescription' => 'description',
            'setDatePostedAsString' => 'created_at',
            'setLocation' => 'location',
            'setName' => 'title',
            'setSourceId' => 'id',
            'setUrl' => 'url',
            'setWorkHours' => 'type',
        ];
    }

    /**
     * Get listings path.
     *
     * @return  string
     */
    public function getListingsPath()
    {
        return '';
    }

    /**
     * Retrieves query string.
     *
     * @return string
     */
    protected function getQueryString()
    {
        $query = http_build_query($this->searchParameters);

        if ($query) {
            $query = '&' . $query;
        }

        return $query;
    }

    /**
     * Get url.
     *
     * @return  string
     */
    public function getUrl()
    {
        return 'https://jobs.github.com/positions.'.$this->getFormat().
            ($this->getQueryString() ? '?' . $this->getQueryString() : '');
    }

    /**
     * Get http verb.
     *
     * @return  string
     */
    public function getVerb()
    {
        return 'GET';
    }

    public function setFullTime($value)
    {
        $fullTime = (bool) $value ? '1' : null;

        return $this->updateQuery($fullTime, 'full_time');
    }

    public function setCityStateLocation($job)
    {
        if (isset($job->location)) {
            $locationArray = static::parseLocation($job->location);

            if (isset($locationArray[0])) {
                $job->setCity($locationArray[0]);
            }
            if (isset($locationArray[1])) {
                $job->setState($locationArray[1]);
            }
        }

        return $job;
    }

    /**
     * Attempts to update current query parameters.
     *
     * @param  string  $value
     * @param  string  $key
     *
     * @return Github
     */
    protected function updateQuery($value, $key)
    {
        if (array_key_exists($key, $this->searchParameters)) {
            $this->searchParameters[$key] = $value;
        }

        return $this;
    }
}
