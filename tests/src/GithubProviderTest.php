<?php namespace JobApis\Jobs\Client\Tests;

use DateTime;
use JobApis\Jobs\Client\Job;
use JobApis\Jobs\Client\Providers\Github;
use Mockery as m;

class GithubProviderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->params = [];

        $this->client = new Github($this->params);
    }

    public function testClientUsesListingsPath()
    {
        $listingsPath = $this->client->getListingsPath();

        $this->assertEquals('', $listingsPath);
    }

    public function testClientUsesJsonFormat()
    {
        $format = $this->client->getFormat();

        $this->assertEquals('json', $format);
    }

    public function testClientUsesGetMethod()
    {
        $verb = $this->client->getVerb();

        $this->assertEquals('GET', $verb);
    }

    public function testUrlDoesNotContainQueryStringWhenNoParamsSet()
    {
        $url = $this->client->getUrl();

        $this->assertNotContains('?', $url);
    }

    public function testUrlContainsSearchParametersWhenProvided()
    {
        $client = new \ReflectionClass(Github::class);
        $property = $client->getProperty("searchMap");
        $property->setAccessible(true);
        $searchMap = $property->getValue($this->client);

        $searchParameters = array_values($searchMap);
        $params = [];

        array_map(function ($item) use (&$params) {
            $params[$item] = uniqid();
        }, $searchParameters);

        $newClient = new Github(array_merge($this->params, $params));

        $url = $newClient->getUrl();

        array_walk($params, function ($v, $k) use ($url) {
            $this->assertContains('?', $url);
            $this->assertContains($k.'='.$v, $url);
        });
    }

    public function testUrlContainsSearchParametersWhenSet()
    {
        $client = new \ReflectionClass(Github::class);
        $property = $client->getProperty("searchMap");
        $property->setAccessible(true);
        $searchMap = $property->getValue($this->client);

        array_walk($searchMap, function ($v, $k) {
            $value = uniqid();
            $url = $this->client->$k($value)->getUrl();

            $this->assertContains('?', $url);
            $this->assertContains($v.'='.$value, $url);
        });
    }

    public function testUrlContainsFullTimeEqualToOneWhenTruthyOptionsProvided()
    {
        $options = [1, -2, 'foo', 2.3e5, true, array(2), "false"];

        array_map(function ($option) {
            $url = $this->client->setFullTime($option)->getUrl();

            $this->assertContains('full_time=1', $url);
        }, $options);
    }

    public function testUrlContainsFullTimeEqualTo0WhenFalseyOptionsProvided()
    {
        $options = [0, '', false, array(), null];

        array_map(function ($option) {
            $url = $this->client->setFullTime($option)->getUrl();

            $this->assertNotContains('full_time', $url);
        }, $options);
    }

    public function testCreateJobObject()
    {
        $json = $this->getListingJson();
        $payload = json_decode($json, true);

        $job = $this->client->createJobObject($payload);

        $this->assertInstanceOf(Job::class, $job);
        $this->assertEquals($payload['id'], $job->getSourceId());
        $this->assertEquals($payload['title'], $job->getName());
        $this->assertEquals($payload['description'], $job->getDescription());
        $this->assertEquals($payload['location'], $job->getLocation());
        $this->assertEquals($payload['type'], $job->getWorkHours());
        $this->assertEquals($payload['url'], $job->getUrl());
        $this->assertEquals(new DateTime(date('Y-m-d H:i:s', strtotime($payload['created_at']))), $job->getDatePosted());
        $this->assertEquals($payload['company'], $job->getCompanyName());
        $this->assertEquals($payload['company_logo'], $job->getCompanyLogo());
        $this->assertEquals($payload['company_url'], $job->getCompanyUrl());
        $this->assertContains($job->getJobLocation()->getAddress()->getAddressLocality(), $payload['location']);
    }

    protected function getListingJson()
    {
        return '{
            "id": "6df24250-3fa0-11e5-86b5-f121d4f70ffa",
            "created_at": "Mon Aug 10 20:44:37 UTC 2015",
            "title": "Python / Full Stack engineer - Innovative Digital Health company with top talent and investors",
            "location": "San Francisco",
            "type": "Full Time",
            "description": "<h1>Python Full Stack Software Engineer</h1>\n\n<p>Virta is a science-based digital health company that solves the #1 health challenge of our generation. Virta was founded by successful second time tech entrepreneurs, scientists and physicians and we are backed by some of the Silicon Valley’s most prominent individual and institutional investors.  Our small, crazy-smart team is based in San Francisco. </p>\n\n<p>We are looking for a talented full-stack engineer to join our top-notch mission-driven team.  If you meet the requirements below, we would love to tell you more about Virta in person.</p>\n\n<h3>Minimum Requirements:</h3>\n\n<ul>\n<li>4+ years of experience shipping full-stack production code in high quality environments </li>\n<li>Well versed in python, javascript, and their popular frameworks and toolsets</li>\n<li>Has great attention to detail, carefully crafts both tests and code </li>\n<li>Has designed and built scalable APIs</li>\n<li>Loves learning new technologies</li>\n</ul>\n\n<h3>Alignment with our Virta culture:</h3>\n\n<ul>\n<li>Demonstrated passion towards our purpose improving the health of 100s of millions of people</li>\n<li>Experienced and thrives in a chaotic, fast-growing startup environment</li>\n<li>Demonstrated resourcefulness and have completed ambitious projects outside of work / school</li>\n<li>High intellectual horsepower - able to keep up with the rest of the Virta crew.  :)</li>\n</ul>\n\n<h3>Responsibilities:</h3>\n\n<ul>\n<li>Code and ship complex new features across the full software stack at high velocity with high quality</li>\n<li>Troubleshoot debug, and fix issues in production and non-production environments</li>\n<li>Coach and mentor peer engineers to become great developers and recommend best practices and tools.</li>\n<li>Help out with DevOps and IT tasks as necessary</li>\n</ul>\n\n<h3>Within your first 90 days at Virta, we expect you will do the following:</h3>\n\n<ul>\n<li>Code and ship at least one new complex feature that involves both front and back end code with full test coverage </li>\n<li>Teach and inspire other engineering team members through knowledge sharing, pair programming, and/or giving feedback during code reviews</li>\n<li>Propose and implement one or more process improvements to make our engineering team even better</li>\n</ul>\n\n<h3>If this sounds like the kind of work you would love to be doing, reach out to us and let’s chat about how we might combine forces!</h3>\n",
            "how_to_apply": "<p>Contact us at <a href=\"mailto:jobs@virtahealth.com\">jobs@virtahealth.com</a></p>\n",
            "company": "Virta Health",
            "company_url": "http://www.virtahealth.com/",
            "company_logo": "http://github-jobs.s3.amazonaws.com/181e1c1e-3fa0-11e5-8561-11e91b7e1dfb.png",
            "url": "http://jobs.github.com/positions/6df24250-3fa0-11e5-86b5-f121d4f70ffa"
        }';
    }
}
