<?php 
namespace App\helpers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Psr\Log\LoggerInterface;
use Illuminate\Support\Str;
use App\Models\QueueRoom;
use App\Helpers\Logics;

class Fileupload
{   
    /** This function using for upload static .html file to s3 for the particuler queue room according to queue room ID */ 
    public static function uploadFileInS3($file, $type, $userId, $roomId, $uploadedFrom = null)
    {
        /** upload .html file in S3 bucket | start */
        // generate a unique bucket name
        // $bucketName = 'static-website-' . $type .'-' . $userId;
        $bucketName = 'static-website-' . $type .'-'.$roomId. '-' . $userId;

        // Initialize the S3 client
        $s3 = new S3Client([
            'version' => 'latest',
            'region'  => env('AWS_DEFAULT_REGION'),
            'credentials' => [
                'key'    => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);

        try {
            // Check if the bucket exists by listing all buckets
            $bucketExists = Logics::checkBucket($bucketName);
        
            // If the bucket does not exist, create it
            if (!$bucketExists) {
                $s3->createBucket([
                    'Bucket' => $bucketName,
                    'CreateBucketConfiguration' => [
                        'LocationConstraint' => env('AWS_DEFAULT_REGION'),
                    ],
                ]);
            
                // Wait until the bucket is created
                $s3->waitUntil('BucketExists', ['Bucket' => $bucketName]);
            
                // Configure the bucket as a static website
                $s3->putBucketWebsite([
                    'Bucket' => $bucketName,
                    'WebsiteConfiguration' => [
                        'IndexDocument' => [
                            'Suffix' => 'index.html',
                        ],
                    ],
                ]);
            
                // Update bucket public access settings
                $s3->putPublicAccessBlock([
                    'Bucket' => $bucketName,
                    'PublicAccessBlockConfiguration' => [
                        'BlockPublicAcls' => false,
                        'IgnorePublicAcls' => false,
                        'BlockPublicPolicy' => false,
                        'RestrictPublicBuckets' => false,
                    ],
                ]);
            
                // Set bucket policy to make it public
                $bucketPolicy = [
                    "Version" => "2012-10-17",
                    "Statement" => [
                        [
                            "Sid" => "PublicReadGetObject",
                            "Effect" => "Allow",
                            "Principal" => "*",
                            "Action" => [
                                "s3:GetObject"
                            ],
                            "Resource" => [
                                "arn:aws:s3:::$bucketName/*"
                            ],
                        ],
                    ],
                ];
            
                $s3->putBucketPolicy([
                    'Bucket' => $bucketName,
                    'Policy' => json_encode($bucketPolicy),
                ]);
            }
        
            if ($uploadedFrom == 1) {
                // Upload the HTML file to the bucket
                $s3->putObject([
                    'Bucket' => $bucketName,
                    'Key'    => 'index.html',
                    'Body'   => html_entity_decode($file),
                    'ContentType' => 'text/html',
                ]);
            } else {
                // Upload the HTML file to the bucket
                $s3->putObject([
                    'Bucket' => $bucketName,
                    'Key'    => 'index.html',
                    'Body'   => file_get_contents($file),
                    'ContentType' => 'text/html',
                ]);
            }
        
            // Construct the website URL
            // $websiteUrl = "https://{$bucketName}.s3." . env('AWS_DEFAULT_REGION') . ".amazonaws.com/index.html";
            $websiteUrl = "http://{$bucketName}.s3-website." . env('AWS_DEFAULT_REGION') . ".amazonaws.com";
        
            return $websiteUrl;
        } catch (AwsException $e) {
            // Log the error details
            return response()->json([
                'status' => false,
                'type' => 'fail',
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $err) {
            // Log the error details
            error_log($err->getMessage());
            return response()->json([
                'status' => false,
                'type' => 'fail',
                'message' => $err->getMessage()
            ], 422);
        }
        /** upload .html file in S3 bucket | end */
    }

    /** This function using for upload static .html file to s3 for the template according to template ID */
    public static function uploadTemplateInS3($file, $type, $userId, $templateId)
    {
        /** uploading template in S3 bucket with create the bucket | start */
        $bucketName = 'static-template-' . $type .'-'.$templateId. '-' . $userId;

        // Initialize the S3 client
        $s3 = new S3Client([
            'version' => 'latest',
            'region'  => env('AWS_DEFAULT_REGION'),
            'credentials' => [
                'key'    => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);

        // Check if the bucket exists by listing all buckets
        $bucketExists = Logics::checkBucket($bucketName);
    
        // If the bucket does not exist, create it
        if (!$bucketExists) {
            $s3->createBucket([
                'Bucket' => $bucketName,
                'CreateBucketConfiguration' => [
                    'LocationConstraint' => env('AWS_DEFAULT_REGION'),
                ],
            ]);
        
            // Wait until the bucket is created
            $s3->waitUntil('BucketExists', ['Bucket' => $bucketName]);
        
            // Configure the bucket as a static website
            $s3->putBucketWebsite([
                'Bucket' => $bucketName,
                'WebsiteConfiguration' => [
                    'IndexDocument' => [
                        'Suffix' => 'index.html',
                    ],
                ],
            ]);
        
            // Update bucket public access settings
            $s3->putPublicAccessBlock([
                'Bucket' => $bucketName,
                'PublicAccessBlockConfiguration' => [
                    'BlockPublicAcls' => false,
                    'IgnorePublicAcls' => false,
                    'BlockPublicPolicy' => false,
                    'RestrictPublicBuckets' => false,
                ],
            ]);
        
            // Set bucket policy to make it public
            $bucketPolicy = [
                "Version" => "2012-10-17",
                "Statement" => [
                    [
                        "Sid" => "PublicReadGetObject",
                        "Effect" => "Allow",
                        "Principal" => "*",
                        "Action" => [
                            "s3:GetObject"
                        ],
                        "Resource" => [
                            "arn:aws:s3:::$bucketName/*"
                        ],
                    ],
                ],
            ];
        
            $s3->putBucketPolicy([
                'Bucket' => $bucketName,
                'Policy' => json_encode($bucketPolicy),
            ]);
        }
    
        // if ($uploadedFrom == 1) {
            // Upload the HTML file to the bucket
            $s3->putObject([
                'Bucket' => $bucketName,
                'Key'    => 'index.html',
                'Body'   => html_entity_decode($file),
                'ContentType' => 'text/html',
            ]);
        // } else {
        //     // Upload the HTML file to the bucket
        //     $s3->putObject([
        //         'Bucket' => $bucketName,
        //         'Key'    => 'index.html',
        //         'Body'   => file_get_contents($file),
        //         'ContentType' => 'text/html',
        //     ]);
        // }
    
        // Construct the website URL
        $websiteUrl = "https://{$bucketName}.s3." . env('AWS_DEFAULT_REGION') . ".amazonaws.com/index.html";
    
        return $websiteUrl;
        /** uploading template in S3 bucket with create the bucket | end */
    }
    
}
?>