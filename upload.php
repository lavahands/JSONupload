<?php

function showResult($text, $isError = true)
{
    return "<p class='" . ($isError ? 'msg-error' : 'msg-success') . "'>" . htmlspecialchars($text) . "</p>";
}

function validName($string)
{
    return !preg_match('/^(\w+)(\s?)(\d+)$/', $string);
}
function validPhone($string)
{
    return !preg_match('/^(?!-)(?=.[0-9])[- ()0-9]+(?<!-)$/', $string);
}
function validAddress($string)
{
    return !preg_match('/^[\w]+[\w\/., ]+$/', $string);
}
function validWebsite($string)
{
    return !preg_match('/^(?:(?:(?:[a-zA-z\-]+)\:\/{1,3})?(?:[a-zA-Z0-9])(?:[a-zA-Z0-9-\.]){1,61}[a-zA-Z0-9](?:\.[a-zA-Z]{2,})(\/\S*)?)$/', $string);
}

function isMultiArray($arrays)
{
    foreach ($arrays as $array) {
        if (is_array($array)) return true;
    }
    return false;
}

function checkKeys($array, $obj)
{
    if (!isset($array['name'])) {
        $jerrors[] = 'JSON object number: ' . $obj . ' is missing key "name"';
    }
    if (!isset($array['phone'])) {
        $jerrors[] = 'JSON object number: ' . $obj . ' is missing key "phone"';
    }
    if (!isset($array['address'])) {
        $jerrors[] = 'JSON object number: ' . $obj . ' is missing key "address"';
    }
    if (!isset($array['website'])) {
        $jerrors[] = 'JSON object number: ' . $obj . ' is missing key "website"';
    }
}

//faili loogika
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
        $errors = [];
        $ext = 'json'; //lubatud faililaiend
        $path = './uploads/'; // WWWst rootist välja ?chmod("/uploads/sha1Named", 0644); + .htaccess?
        $file_name = $_FILES['file']['name'];
        $file_size = $_FILES['file']['size'];
        $file_tmp = $_FILES['file']['tmp_name'];
        $file_type = $_FILES['file']['type'];
        $file_ext = explode('.', $_FILES['file']['name']);
        // suuruse kontroll
        if ($file_size > 10485760) {
            $errors[] = 'File size exceeds limit: ' . $file_name . ' ' . $file_type;
        }
        // laiendi kontroll
        if ($ext !== strtolower(end($file_ext))) {
            $errors[] = 'Forbidden extention: ' . $file_name . ' ' . $file_type;
        }
        // mime
        $mime = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $file_tmp);
        if ($mime !== 'text/plain') {
            $errors[] = 'Invalid file format: ' . $mime;
        }

        if (empty($errors)) {
            $jerrors = [];
            $jsondata = file_get_contents($file_tmp);
            $requiredKeys = ['name', 'phone', 'address', 'website'];
            $json = json_decode($jsondata, true);
            if ($json == null || json_last_error() !== JSON_ERROR_NONE) {
                $jerrors[] = 'Invalid JSON format';
            } else {
                if (isMultiArray($json)) {
                    $objectNumber = 0;
                    foreach ($json as $array => $keys) {
                        $objectNumber++;
                        checkKeys($json[$array], $objectNumber);
                        foreach ($json[$array] as $key => $value) {
                            switch ($key) {
                                case "name":
                                    if (validName($value)) {
                                        $jerrors[] = 'JSON object number: ' . $objectNumber . ' value of key "name" is incorrect';
                                    }
                                    break;
                                case "address":
                                    if (validAddress($value)) {
                                        $jerrors[] = 'JSON object number: ' . $objectNumber . ' value of key "address" is incorrect';
                                    }
                                    break;
                                case "phone":
                                    if (validPhone($value)) {
                                        $jerrors[] = 'JSON object number: ' . $objectNumber . ' value of key "phone" is incorrect';
                                    }
                                    break;
                                case "website":
                                    if (validWebsite($value)) {
                                        $jerrors[] = 'JSON object number: ' . $objectNumber . ' value of key "website" is incorrect';
                                    }
                                    break;
                                default:
                                    $jerrors[] = 'JSON object number: ' . $objectNumber . ' contains illegal key: ' . $key;
                            }
                        }
                    }
                } else {
                    checkKeys($json, 1); //Single object json file
                    foreach ($json as $key => $value) {
                        switch ($key) {
                            case "name":
                                if (validName($value)) {
                                    $jerrors[] = 'Value of key "name" is incorrect';
                                }
                                break;
                            case "address":
                                if (validAddress($value)) {
                                    $jerrors[] = 'Value of key "address" is incorrect';
                                }
                                break;
                            case "phone":
                                if (validPhone($value)) {
                                    $jerrors[] = 'Value of key "phone" is incorrect';
                                }
                                break;
                            case "website":
                                if (validWebsite($value)) {
                                    $jerrors[] = 'Value of key "website" is incorrect';
                                }
                                break;
                            default:
                                $jerrors[] = 'JSON object contains illegal key: ' . $key;
                        }
                    }
                }
            }
            // JSON vigade puudumisel faili ümbernimetamine sha1-e ja liigutamine /uploads kausta
            if (empty($jerrors)) {
                if (!move_uploaded_file($file_tmp, sprintf('./uploads/%s.%s', sha1_file($file_tmp), $ext))) {
                    $errors[] = 'Failed to move the uploaded file.';
                } else {
                    echo showResult('File was uploaded successfully', false);
                }
            } else {
                foreach ($jerrors as $jerror) {
                    echo showResult($jerror, true);
                }
                echo showResult('Due to errors JSON file was not uploaded.', true);
            }
        }

        if ($errors) {
            foreach ($errors as $err) {
                echo showResult($err, true);
            }
        }
    }
}
