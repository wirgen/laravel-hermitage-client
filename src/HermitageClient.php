<?php

namespace Wirgen\HermitageClient;

use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\UploadedFile;
use livetyping\hermitage\client\Client;
use livetyping\hermitage\client\signer\RequestSigner;
use livetyping\hermitage\client\signer\Signer;
use Psr\Http\Message\UriInterface;

class HermitageClient
{
    protected $app;

    protected $client;

    /**
     * HermitageClient constructor.
     *
     * @param Application $app
     * @throws Exception
     */
    public function __construct(Application $app = null)
    {
        if (!$app) {
            $app = app();
        }

        $baseUri = $app['config']->get('hermitage.baseUri', null);
        if (!$baseUri) {
            throw new Exception("Hermitage URL not set");
        }

        $this->client = new Client(
            new RequestSigner(new Signer($app['config']->get('hermitage.secret', ''))),
            new \GuzzleHttp\Client(),
            $baseUri);
    }

    /**
     * Get file binary from hermitage
     *
     * @param string $filename
     * @param string $version
     * @return string
     */
    public function get($filename, $version = '')
    {
        $response = $this->client->get($filename, $version);

        return (string)$response->getBody();
    }

    /**
     * Upload UploadedFile from form to hermitage
     * Return file name in hermitage
     *
     * @param UploadedFile $file
     * @return string
     * @throws FileNotFoundException
     */
    public function uploadFile(UploadedFile $file)
    {
        return $this->upload($file->get());
    }

    /**
     * Upload file to hermitage by URI
     * Return file name in hermitage
     *
     * @param string $uri
     * @return string
     */
    public function uploadByURI(string $uri)
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->get($uri);

        return $this->upload($response->getBody());
    }

    /**
     * Upload image to hermitage
     * Return file name in hermitage
     *
     * @param string $image Binary image
     * @return string
     */
    public function upload(string $image)
    {
        $response = $this->client->upload($image);
        $data = json_decode((string)$response->getBody());

        return $data->filename;
    }

    /**
     * Delete file from hermitage
     *
     * @param string $filename
     */
    public function delete($filename)
    {
        $this->client->delete($filename);
    }

    /**
     * Get uri for file in hermitage
     *
     * @param string $filename
     * @param string $version
     * @return string
     */
    public function getUri($filename, $version = '')
    {
        return (string)$this->uriFor($filename, $version);
    }

    /**
     * Get Uri object for file in hermitage
     *
     * @param string $filename
     * @param string $version
     * @return UriInterface
     */
    public function uriFor($filename, $version = '')
    {
        return $this->client->uriFor($filename, $version);
    }
}
