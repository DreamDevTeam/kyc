<?php


namespace App\Http\Services;


use App\Helpers\Image;
use App\Helpers\RecursiveDirSearch;
use App\Helpers\RecursiveSearch;
use App\Helpers\StateChecker;
use App\Helpers\Tier;
use App\Http\Controllers\BaseController;
use App\Http\Services\AnalogValidation\DocType;
use App\Http\Services\AnalogValidation\Expiry;
use App\Http\Services\AnalogValidation\ImageQA;
use App\Http\Services\AnalogValidation\Text;
use App\Models\Customers;
use DateTime;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class AnalogService extends BaseController
{
    private string $tid;

    /**
     * @param object $request
     * @return array|\Illuminate\Http\RedirectResponse|string
     * @throws \Exception
     */
    public function process(object $request)
    {
        $response = Http::get(env('PAYMENTIQ_URL') . '/api/link/unhash/' . $request->hash);
        $unhash = $response->json();

        $result = [];
        $result['type'] = $unhash['type'];
        $result['like'] = $unhash['like'];
        $result['tg_name'] = $unhash['tg_name'] ?? null;
        $result['pay_id'] = $unhash['pay_id'] ?? null;

        $this->tid = self::getTid(24);

        $compressionPhoto = [];
        $images = $request->only(['front', 'back', 'selfie']);
        foreach ($images as $key => $image) {
            $compressionPhoto[$key] = Image::resizeEncodeBase64($image);
        }

//        dd($unhash);

        if ($unhash['type'] === 'update') {
            $this->matchOldAndNewDocs(photos: $compressionPhoto, payId: $unhash['pay_id']);
            $customer = Customers::where('payId', $unhash['pay_id'])->first();


            $result['dob'] = Carbon::parse($customer->dob)->format('Y-m-d');
            $result['email'] = $customer->email;
            $result['mobile'] = $customer->mobile;
            $result['gender'] = match (true) {
                $customer->gender === 'Male' => 'Male',
                $customer->gender === 'Female' => 'Female',
                default => ''
            };
        }

        $documentReader = self::documentReader($compressionPhoto, $this->tid);


        return [...$result, ...$documentReader];
    }


    public function matchOldAndNewDocs(array $photos, string $payId)
    {
        if (!empty($getPhotosData = $this->getPhotos(payId: $payId))) {

            #Old logic
//            $oldPhotos = array_combine(['selfie', 'front', 'back'], $getPhotosData);

            #New logic
            $keys = ['selfie', 'front', 'back'];
            $values = $getPhotosData;

            if (count($keys) === count($values)) {
                $oldPhotos = array_combine($keys, $values);
            } else {
                $minCount = min(count($keys), count($values));
                $combined = array_combine(
                    array_slice($keys, 0, $minCount),
                    array_slice($values, 0, $minCount)
                );

                if (count($keys) > $minCount) {
                    foreach (array_slice($keys, $minCount) as $missingKey) {
                        $combined[$missingKey] = null;
                    }
                }
                $oldPhotos = $combined;
            }


            $documentMatch = Http::post('http://' . env('REGULA_IP_FACE') . ':41101/api/match', [
                "images" => [
                    [
                        "type" => 1,
                        "data" => str_replace('data:image/jpeg;base64,', '', $oldPhotos['front']),
                        "detectAll" => false,
                    ],
                    [
                        "type" => 1,
                        "data" => str_replace('data:image/jpeg;base64,', '', $photos['front']),
                        "detectAll" => false,
                    ],
                ],
            ]);


            $documentMatchResult = $documentMatch->json();
//            $error = (new RecursiveSearch)->index(array: $documentMatchResult, searchValue: 'errorMsg');
//            if ($error) {
//                throw new \Exception("Your front document image face not detected.", 400);
//            }

//            dd((new RecursiveSearch)->index(array: $documentMatchResult, searchValue: 'similarity'));
            $isSimilarity = (new RecursiveSearch)->index(array: $documentMatchResult, searchValue: 'similarity');
            if(!is_null($isSimilarity)) {
                $similarity = round((new RecursiveSearch)->index(array: $documentMatchResult, searchValue: 'similarity')['similarity'], 2) * 100;
                if ($similarity < 75) {
                    throw new \Exception("Your current person data doesn't match the old ones.", 400);
                }
            }

        }
    }


    public function documentReader(array $photos, string $tid): array|string
    {
        $documentsError = false;
        $documentsErrorText = null;

        $documentSelfieResponse = Http::post('http://' . env('REGULA_IP_READER') . ':8080/api/process',
            self::makeDokSelfie($photos));

//        $isPercentValue = (new RecursiveSearch)->index(array: $documentSelfieResponse->json(), searchValue: 'PercentValue');
//        dd($isPercentValue);
        $similaritySelfie = (new RecursiveSearch)->index(array: $documentSelfieResponse->json(), searchValue: 'PercentValue');
        if (is_null($similaritySelfie)) {
            $documentsError = true;
            $documentsErrorText = "Couldn't find the selfie file";
        }

        if(!is_null($similaritySelfie)) {
            $similarityPercentSelfie = match (true) {
                is_array($similaritySelfie) => (new RecursiveSearch)->indexData(array: $similaritySelfie, searchValue: 'PercentValue')['PercentValue'],
                default => $similaritySelfie['PercentValue']
            };
            if ($similarityPercentSelfie < 20) {
                $documentsError = true;
                $documentsErrorText = "Your selfie doesn't match the document. Rate: $similarityPercentSelfie %";
            }
            if ($similarityPercentSelfie < 75 && $similarityPercentSelfie > 20) {
                $documentsError = true;
                $documentsErrorText = "The quality of your document on the selfie is low. Pleas take a new photo. Rate: $similarityPercentSelfie %";
            }
        }


        $documentReaderResponse = Http::post('http://' . env('REGULA_IP_READER') . ':8080/api/process',
            self::makeDokReader($photos, $tid));

        if ($documentReaderResponse->successful()) {

            $documentReaderResult = $documentReaderResponse->json();
            $getStatusCheck = (new RecursiveSearch)->indexData(array: $documentReaderResult, searchValue: 'detailsOptical');

            $similarity = (new RecursiveSearch)->index(array: $documentReaderResult, searchValue: 'PercentValue');
            if(is_null($similarity)) {
                $documentsError = true;
                $documentsErrorText = "The quality of your document on the selfie is low. Pleas take a new photo!";
            }

            $similarityPercent = match (true) {
                is_array($similarity) => (new RecursiveSearch)->indexData(array: $similarity, searchValue: 'PercentValue')['PercentValue'],
                default => $similarity['PercentValue']
            };
            if ($similarityPercent < 20) {
                $documentsError = true;
                $documentsErrorText = "Your selfie doesn't match the document";
            }

            if ($similarityPercent < 75 && $similarityPercent > 20) {
                $documentsError = true;
                $documentsErrorText = "Your front side document hard to read.";
            }


            if (!$getStatusCheck['detailsOptical']['overallStatus']) {
                $analogValidation = match (true) {
                    $getStatusCheck['detailsOptical']['expiry'] !== 1 => Expiry::validation(),
                    !($getStatusCheck['detailsOptical']['docType']) => DocType::validation(
                        (new RecursiveSearch)->indexData(array: $documentReaderResult, searchValue: 'OneCandidate')
                    ),
                    !($getStatusCheck['detailsOptical']['imageQA']) => ImageQA::validation(
                        (new RecursiveSearch)->indexData(array: $documentReaderResult, searchValue: 'ImageQualityCheckList')
                    ),
                    !($getStatusCheck['detailsOptical']['text']) => Text::validation(
                        (new RecursiveSearch)->indexData(array: $documentReaderResult, searchValue: 'Text')
                    ),
                    default => 'Unsupported  document type!'
                };
                $documentsError = true;
                $documentsErrorText = $analogValidation;
            }


            $fields = (new RecursiveSearch)->indexParent(array: $documentReaderResponse->json(), searchValue: 'fieldType');
            $fields[0]['documentsError'] = $documentsError;

            if($documentsError) {
                $fields[0]['documentsErrorText'] = $documentsErrorText;
            }

            $documentWithoutExpiry = ['1661136641'];
            $checkType = array_column((new RecursiveSearch)->index(array: $documentReaderResponse->json(), searchValue: 'ID',flag: 'k') ?? [], 'ID');

           if(!empty(array_intersect($documentWithoutExpiry, $checkType))) {
               $getFieldTypes = (new RecursiveSearch)->indexParent(array: $documentReaderResponse->json(), searchValue: 'FieldType');
                foreach ($getFieldTypes as $getFieldType) {
                    if($getFieldType['FieldType'] === 391) {
                        $fields = array_merge($fields,array(["fieldType" => 3, "value" => $getFieldType['Field_Visual']]));
                    }
                }
           }

           return $this->getDocumentText($fields);
        }
    }

    public function getPhotos($payId = null, $regulaTid = null, $cid = null): array|string
    {
        $imagesBase64 = [];
        $customer = Customers::query()
            ->when(!is_null($regulaTid) && !is_null($cid), function ($query) use ($regulaTid, $cid) {
                return $query->where([['id', $cid], ['regulaTid', $regulaTid]]);
            })
            ->when(!is_null($payId), function ($query) use ($payId) {
                return $query->where('payId', $payId);
            })
            ->first();


        if (!is_null($customer->register_date) || !is_null($customer->created_at)) {
            $dataKey = !is_null($customer->register_date) ? 'register_date' : 'created_at';
            $dataRegulaTid = !is_null($regulaTid) ? $regulaTid : $customer->regulaTid;

            $dirDocumentReaderWebapi = self::createPath(
                rootPath: "/opt/regula/document-reader-webapi/output/",
                customer: $customer,
                key: $dataKey
            );


            $dirFaceWebapi = self::createPath(
                rootPath: "/opt/regula/face-rec-service/faceapi-detect-match/compare_json/default/", //adapter
                customer: $customer,
                key: $dataKey
            );


            if (!is_null($dataRegulaTid)) {
                $documentReader = (new RecursiveDirSearch)->index($dirDocumentReaderWebapi, $dataRegulaTid);

                if (!empty($documentReader)) {
                    $contentR = file_get_contents($documentReader[0]);
                    $imageDataR = json_decode($contentR, true);


                    if(isset($imageDataR['extPortrait'])) {
                        $imagesBase64[] = $imageDataR['extPortrait'];
                    }
                    foreach ($imageDataR['List'] as $img) {
                        $imagesBase64[] = str_contains($img['ImageData']['image'],'data:image/jpeg;base64,') ?
                            $img['ImageData']['image'] :
                            'data:image/jpeg;base64,'.$img['ImageData']['image'];
                    }
                }

                if(count($imagesBase64) !== 3) {
                    $documentFace = (new RecursiveDirSearch)->index($dirFaceWebapi, $dataRegulaTid);
                    if (!empty($documentFace)) {
                        $contentF = file_get_contents($documentFace[0]);
                        $imageDataF = json_decode($contentF, true);

                        foreach ($imageDataF['images'] as $image) {
                            $imagesBase64[] = 'data:image/jpeg;base64,'.$image['data'];
                        }
                    }
                }

            }
        }

        return $imagesBase64;
    }

    private function getDocumentText(array $fields): array
    {
        $result = [];
        $result['tid'] = $this->tid;
        $result['documentsError'] = $fields[0]['documentsError'];

        if($fields[0]['documentsError']){
            $result['documentsErrorText'] = $fields[0]['documentsErrorText'];
        }


        foreach ($fields as $field) {
            switch ($field['fieldType']) {
                case 25:
                    $result['firstname'] = $field['value'];
                    break;
                case 3:
                    $result['expiry_date'] = $field['value'];
                    break;
                case 8:
                    $result['lastname'] = $field['value'];
                    break;
                case 5:

                    $date = DateTime::createFromFormat('Y-m-d', $field['value']);
                    if ($date && $date->format('Y-m-d') === $field['value']) {
                        $result['dob'] = $field['value'];
                    } else {
                        $result['dob'] = null;
                    }

//                    $result['dob'] = $field['value'];
                    break;
                case 185:
                    $result['age'] = $field['value'];
                    break;
                case 77:
                    $result['city'] = $field['value'];
                    break;
                case 79:
                    $result['postcode'] = $field['value'];
                    break;
                case 65:
                    $result['state'] = StateChecker::index($field['value']);
                    break;
                case 17:
                    $result['address'] = str_replace('^', ' ', $field['value']);
                    break;
            }
        }

        if(!isset($result['expiry_date'])) {
            $expire = null;
            if(isset($fields[0]['value']) && $fields[0]['value']) {
                $expire = $fields[0]['value'];
            }

            // закоментировать для exoire - null
            if(!preg_match('/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/', $expire)) {
                $expire = Carbon::now();
            }

            $result['expiry_date'] = $expire;
        }

        if (isset($result['firstname'])) {
            $sepFirstName = explode(' ', $result['firstname']);
            if (count($sepFirstName) >= 3) {
                $result['firstname'] = $sepFirstName[1] ?? null;
                $result['middlename'] = $sepFirstName[2] ?? null;
            }
        }
        return $result;
    }

    private static function makeDokReader(array $photos, string $tid): array
    {
        return [
            "tag" => $tid,
            "processParam" => [
                "scenario" => "FullProcess",
                "authParams" => [
                    "checkLiveness" => true,
                ],
                "useFaceApi" => true,
                "oneShotIdentification" => true,
                "threshold" => 75,
                "minimalHolderAge" => 18,
            ],
            "List" => [
                [
                    "ImageData" => [
                        "image" => $photos['front'],
                    ],
                    "page_idx" => 0,
                ],
                [
                    "ImageData" => [
                        "image" => $photos['back'],
                    ],
                    "page_idx" => 1,
                ],
            ],
            "extPortrait" => $photos['selfie']
        ];
    }

    private static function makeDokSelfie(array $photos)
    {
        return [
            "processParam" => [
                "scenario" => "FullProcess",
                "authParams" => [
                    "checkLiveness" => true,
                ],
                "useFaceApi" => true,
                "oneShotIdentification" => true,
            ],
            "List" => [
                [
                    "ImageData" => [
                        "image" => $photos['selfie'],
                    ],
                ],
            ],

        ];
    }

    public function getText(object $request):array|string
    {
        $regulaData = [
            'document_type' => null,
            'document_number' => null,
            'document_username' => null,
            'expiry_date' => null,
            'issue_country' => null,
            'nationality' => "AU",
            'birth_date' => null,
            'address_state' => null,
            'address_postcode' => null,
            'address_juricode' => null,
            'address_city' => null,
            'address_street' => null,
            'address' => null,
            'identity_card_number' => null,
        ];


        $customer = Customers::query()->where([['id', $request->cid], ['regulaTid', $request->regulaTid]])->first();
        if(!$customer) {
            throw new \Exception("The current customer's wasn't found.",400);
        }

        $dataKey = !is_null($customer->register_date) ? 'register_date' : 'created_at';

        $dirDocumentReaderWebapi = self::createPath(
            rootPath: "/opt/regula/document-reader-webapi/output/",
            customer: $customer,
            key: $dataKey
        );

        if(!is_dir($dirDocumentReaderWebapi)) {
            throw new \Exception("The current customer's data wasn't found in the regula system.",400);
        }

//        $documentReader = $this->searchFilesByContent($dirDocumentReaderWebapi, $request->regulaTid);
        $documentReader = (new RecursiveDirSearch())->index($dirDocumentReaderWebapi, $request->regulaTid);

        $contentR = file_get_contents($documentReader[1]);

        $textDataR = json_decode($contentR, false);

        $regulaData['datetime'] = $textDataR->TransactionInfo->DateTime;
        foreach ($textDataR->ContainerList->List as $field) {
            if (isset($field->OneCandidate)) {
                $regulaData['document_type'] = $field->OneCandidate->FDSIDList->dDescription;
            }

            if (isset($field->Text)
                && isset($field->Text->fieldList)) {
                foreach ($field->Text->fieldList as $fieldItem) {
                    switch ($fieldItem->fieldType) {
                        case 5:
                            $regulaData['birth_date'] = $fieldItem->value;
                            break;

                        case 3:
                            $regulaData['expiry_date'] = $fieldItem->value;
                            break;

                        case 65:
                            $regulaData['address_state'] = $fieldItem->value;
                            break;

                        case 38:
                            $regulaData['issue_country'] = $fieldItem->value;
                            break;

                        case 2:
                            $regulaData['document_number'] = $fieldItem->value;
                            break;

                        case 25:
                            $regulaData['document_username'] = $fieldItem->value;
                            break;

                        case 79:
                            $regulaData['address_postcode'] = $fieldItem->value;
                            break;

                        case 78:
                            $regulaData['address_juricode'] = $fieldItem->value;
                            break;

                        case 77:
                            $regulaData['address_city'] = $fieldItem->value;
                            break;

                        case 76:
                            $regulaData['address_street'] = $fieldItem->value;
                            break;

                        case 17:
                            $regulaData['address'] = $fieldItem->value;
                            break;

                        case 142:
                            $regulaData['identity_card_number'] = $fieldItem->value;
                            break;
                    }
                }
            }
        }

        return $regulaData;
    }

    private static function createPath(string $rootPath, object $customer, string $key): string
    {
        $registerYear = date("Y", strtotime($customer->$key));
        $registerMonth = date("n", strtotime($customer->$key));
        $registerDay = date("j", strtotime($customer->$key));
        $path = $registerYear . '/' . $registerMonth . '/' . $registerDay;
        return $rootPath . $path;
    }
}
