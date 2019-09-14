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
    return !preg_match('/[^,:{}\\[\\]0-9.\\-+Eaeflnr-u \\n\\r\\t]/', $string);
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
        echo showResult('JSON object number: ' . $obj . ' is missing key "name"', true);
    }
    if (!isset($array['phone'])) {
        echo showResult('JSON object number: ' . $obj . ' is missing key "phone"', true);
    }
    if (!isset($array['address'])) {
        echo showResult('JSON object number: ' . $obj . ' is missing key "address"', true);
    }
    if (!isset($array['website'])) {
        echo showResult('JSON object number: ' . $obj . ' is missing key "website"', true);
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
        if (ext !== strtolower(end($file_ext))) {
            $errors[] = 'Forbidden extention: ' . $file_name . ' ' . $file_type;
        }
        // mime
        $mime = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $file_tmp);
        if ($mime !== 'application/json') {
            $errors[] = 'Invalid file format';
        }

        //nime muutmine ja kausta liigutamine
        if (!move_uploaded_file(
            $file_temp,
            sprintf(
                './uploads/%s.%s',
                sha1_file($file_temp),
                $ext
            )
        )) {
            $errors[] = 'Failed to move the uploaded file.';
        }
    }
}

//json loogika
if (empty($errors)) {
    $jsondata = file_get_contents($fileNewName);
    $json = json_decode($jsondata, true);
    $requiredKeys = ['name', 'phone', 'address', 'website'];
    if (isMultiArray($json)) {
        $objectNumber = 0;
        foreach ($json as $array => $keys) {
            $objectNumber++;
            checkKeys($json[$array], $objectNumber);
            foreach ($json[$array] as $key => $value) {
                switch ($key) {
                    case "name":
                        if (validName($value)) {
                            echo showResult('JSON object number: ' . $objectNumber . ' value of key "name" is incorrect', true);
                        }
                        break;
                    case "address":
                        if (validAddress($value)) {
                            echo showResult('JSON object number: ' . $objectNumber . ' value of key "address" is incorrect', true);
                        }
                        break;
                    case "phone":
                        if (validPhone($value)) {
                            echo showResult('JSON object number: ' . $objectNumber . ' value of key "phone" is incorrect', true);
                        }
                        break;
                    case "website":
                        if (validWebsite($value)) {
                            echo showResult('JSON object number: ' . $objectNumber . ' value of key "website" is incorrect', true);
                        }
                        break;
                    default:
                        echo showResult('JSON object number: ' . $objectNumber . ' contains illegal key: ' . $key, true);
                }
            }
        }
    } else {
        checkKeys($json, 1);
        foreach ($json as $key => $value) {
            switch ($key) {
                case "name":
                    if (validName($value)) {
                        echo showResult('Value of key "name" is incorrect', true);
                    }
                    break;
                case "address":
                    if (validAddress($value)) {
                        echo showResult('Value of key "address" is incorrect', true);
                    }
                    break;
                case "phone":
                    if (validPhone($value)) {
                        echo showResult('Value of key "phone" is incorrect', true);
                    }
                    break;
                case "website":
                    if (validWebsite($value)) {
                        echo showResult('Value of key "website" is incorrect', true);
                    }
                    break;
                default:
                    echo showResult('JSON object contains illegal key: ' . $key, true);
            }
        }
    }
}

//veebileht  ^(?:(?:(?:[a-zA-z\-]+)\:\/{1,3})?(?:[a-zA-Z0-9])(?:[a-zA-Z0-9-\.]){1,61}[a-zA-Z0-9](?:\.[a-zA-Z]{2,}))$
// telo ^(?!-)(?=.[0-9])[- ()0-9]+(?<!-)$

// Errorid
// Kas on json fail ja formaat
// Faili sisuks olev json sisaldab väljasid: nimi, telefoni number, aadress ja veebileht. Peavad
// eksisteerima ja olema täidetud.
// Telefoni number võib sisaldada ainult numbreid (0-9), sidekriipsu (-), tühikut ja sulgusid
// Veebileht peaks olema süntaktiliselt korrektne, ehk sisaldama “domeen.tld” (näiteks
// https://www.scr.ee/minuveeb ja scr.ee on mõlemad lubatavad)
// Faili sees võib olla üks JSON objekt või mittu objekti massiivina.
// Pärast valideerimist tagastada aruanne iga objekti kohta, kas ja millised puudused esinesid.
