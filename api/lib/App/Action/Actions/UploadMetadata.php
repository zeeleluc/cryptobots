<?php
namespace App\Action\Actions;

use App\Action\BaseAction;
use App\Slack;

class UploadMetadata extends BaseAction
{
    public function run(): array
    {
//        $cidImage = $this->uploadImageToIPFS();
//        $metadata = $this->prepareAndGetMetaData($cidImage);
//        $metadataURI = $this->uploadMetaDataToIPFS($metadata);
        return [
            'metadata_ipfs_url' => 'ipfs://bafybeie3jsblpiksu2wqhwudptghfdvn6k6rirhagtr4gixeok3gjfxrue/1.json',
        ];
    }

    private function prepareAndGetMetaData(string $imageCID): array
    {
        $metadata = (array) json_decode(file_get_contents(ROOT . '/data/metadata/1.json'));
        $metadata['image'] = str_replace('REPLACE_WITH_IMAGE_IPFS_URL', $imageCID, $metadata['image']);

        return $metadata;
    }

    private function uploadImageToIPFS()
    {
        // uplaod image
        $apiKey = env('NFT_STORAGE_API_KEY');
        $endpoint = "https://api.nft.storage/upload";

        $filePath = ROOT . '/data/images/1.png';
        $filename = basename($filePath);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $apiKey",
            "Content-Type: multipart/form-data"
        ]);

        $file = new \CURLFile($filePath);
        $file->setPostFilename($filename);

        $data = ["file" => $file];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            (new Slack())->sendErrorMessage(curl_error($ch));
        } else {
            // Decode and print the response
            $responseData = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                (new Slack())->sendErrorMessage(curl_error("JSON Error: " . json_last_error_msg()));
            } else {
                if ($responseData['ok']) {
                    return $responseData['value']['cid'];
                }
            }
        }

        curl_close($ch);
    }


    private function uploadMetaDataToIPFS(array $metadata)
    {
        // Define your API key and the endpoint URL
        $apiKey = env('NFT_STORAGE_API_KEY');
        $endpoint = "https://api.nft.storage/upload";

        // JSON metadata to upload
        $jsonMetadata = json_encode($metadata);

        // Path to the temporary file
        $tempFilePath = sys_get_temp_dir() . '/1.json';

        // Write the JSON metadata to the temporary file
        file_put_contents($tempFilePath, $jsonMetadata);

        // Check if the file was created
        if (!file_exists($tempFilePath)) {
            die("Error: Temporary file could not be created at path: $tempFilePath");
        }

        // Initialize a cURL session
        $ch = curl_init();

        // Set the URL, HTTP headers, and the file to upload
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $apiKey"
        ]);

        // Create a CURLFile object for the temporary file and explicitly set the filename
        $file = new \CURLFile($tempFilePath, 'application/json', '1.json');

        // Attach the file to the POST request
        $data = ["file" => $file];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        // Execute the request
        $response = curl_exec($ch);

        unlink($tempFilePath);

        // Check for errors
        if (curl_errno($ch)) {
            echo 'cURL Error: ' . curl_error($ch);
        } else {
            // Decode and print the response
            $responseData = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                echo "JSON Error: " . json_last_error_msg();
            } else {
                return $responseData['value']['cid'];
            }
        }

        // Close the cURL session
        curl_close($ch);

        // Delete the temporary file

    }
}
