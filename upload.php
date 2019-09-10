<?php

function showResult($text, $isError = true)
{
    return "<p class='" . ($isError ? 'msg-error' : 'msg-success') . "'>" . htmlspecialchars($text) . "</p>";
}
//faili loogika
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
        $errors = [];
        $ext = 'json'; //lubatud faililaiend
        $path = './uploads/'; // WWWst rootist välja
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
        if($mime !== 'application/json') {
            $errors[] = 'Invalid file format';
        }

        //nime muutmine ja kausta liigutamine
        if (!move_uploaded_file(
            $file_temp,
            sprintf('./uploads/%s.%s',
                sha1_file($file_temp),
                $ext
            )
        ))
    }

}

//json loogika
$jsondata = file_get_contents($fileNewName);
$json = json_decode($jsondata, true);
$requiredKeys = ['nimi', 'telefon', 'aadress', 'veebileht'];

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