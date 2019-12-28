<?php

namespace App\Http\Controllers;

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use Aws\Credentials\Credentials;

/**
 * Class UploadsController
 * @package App\Http\Controllers
 */
class UploadsController extends Controller
{
    /**
     * @param $key
     * @return string
     */
    public static function generateUploadUrl($key)
    {
        $credentials = new Credentials(config('constants.aws_key'), config('constants.aws_secret'));
        $s3 = new S3Client([
            'version'     => 'latest',
            'region'      => 'us-east-1',
            'credentials' => $credentials
        ]);

        $cmd = $s3->getCommand('PutObject', [
            'Bucket' => config('constants.s3_bucket_name'),
            'Key' => $key,
            'ACL' => 'authenticated-read',
            'ContentType' => 'binary/octet-stream',
            '@use_accelerate_endpoint' => true,
        ]);

        $response = $s3->createPresignedRequest($cmd, '+20 minutes');
        $presignedUrl = (string) $response->getUri();

        return json_encode([
            'success' => true,
            'url' => $presignedUrl
        ]);
    }

    /**
     * @param $orderProduct
     * @return string
     */
    public static function generateUploadKey($orderProduct)
    {
        return md5($orderProduct->order_id . '-' . $orderProduct->item_id . time());
    }
}
