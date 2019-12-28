<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Helpers\TflowHelper;
use App\Orders;
use DB;

ini_set('memory_limit','2G');
ini_set('max_execution_time', '0');

/**
 * Class UploadArtwork
 * @package App\Jobs
 */
class UploadArtwork implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $orderId;

    /**
     * UploadArtwork constructor.
     * @param $orderId
     */
    public function __construct($orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Log::info('#### Start job UploadArtwork with id: ' . $this->orderId);
        $order = Orders::where('id', $this->orderId)
            ->with(['files'])
            ->first();

        if (!empty($order)) {
            $check_status = DB::select('SELECT count(*) as art_work_status , (SELECT COUNT(*) FROM `order_products` WHERE `order_id` = '.$this->orderId.') as total_product FROM `order_products` WHERE `order_id` = '.$this->orderId.' AND art_work_status >=2');
            if($check_status[0]->art_work_status == $check_status[0]->total_product){
                Orders::where('id', $this->orderId)->update(['customer_status' => 3]);
            }
            \Log::info('#### Process uploadToTflow ' . $this->orderId);
            TflowHelper::uploadToTflow($this->orderId);
        }
    }

    /*public function uploadFiles()
    {
        $credentials = new Credentials(config('constants.aws_key'), config('constants.aws_secret'));
        $s3 = new S3Client([
            'version'     => 'latest',
            'region'      => 'us-east-1',
            'credentials' => $credentials
        ]);

        $user_email = Auth::user()->email;
        $objectKey = md5($user_email.'/'.$filename);

        try {
            $object = $s3->putObject([
                'Bucket' => config('constants.s3_bucket_name'),
                'Key'    => $objectKey,
                'Body'   => fopen($file->getPathName(), 'r'),
                'ACL'    => 'public-read',
            ]);

            $orderImages = DB::table('order_files');
            $data_array['order_id'] = $id;
            $data_array['order_product_id'] = $key;
            $data_array['name'] = $file_name;
            $data_array['s3_key'] = $objectKey;
            $data_array['created_at'] = Carbon::now();
            $data_array['updated_at'] = Carbon::now();
            $orderImages->insertGetId($data_array);

        } catch (Aws\S3\Exception\S3Exception $e) {
            \Session::flash('error', $e->getMessage());
            $res['error_msg'] = 'There was an error uploading your file(s), please try again.';
        }
    }*/
}
