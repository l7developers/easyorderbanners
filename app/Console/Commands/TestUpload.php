<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Aws\S3\S3Client;
use Aws\Credentials\Credentials;
use App\Helpers\Tflow\Client as ApiClient;

class TestUpload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:upload {jobId} {key}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $credentials = new Credentials(config('constants.aws_key'), config('constants.aws_secret'));
        $s3 = new S3Client([
            'version'     => 'latest',
            'region'      => 'us-east-1',
            'credentials' => $credentials
        ]);

        $s3->registerStreamWrapper();
        $fileStream = sprintf('s3://%s/%s', config('constants.s3_bucket_name'), $this->argument('key'));

        $apiClient = new ApiClient(config('constants.Tflow_baseUri'), config('constants.Tflow_clientId'), config('constants.Tflow_clientSecret'));
        $apiClient->uploadArtwork($this->argument('jobId'), $fileStream);
    }
}
