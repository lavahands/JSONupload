<?php

// Errorid
// Kas on json fail ja formaat
// Faili sisuks olev json sisaldab väljasid: nimi, telefoni number, aadress ja veebileht. Peavad
// eksisteerima ja olema täidetud.
// Telefoni number võib sisaldada ainult numbreid (0-9), sidekriipsu (-), tühikut ja sulgusid
// Veebileht peaks olema süntaktiliselt korrektne, ehk sisaldama “domeen.tld” (näiteks
// https://www.scr.ee/minuveeb ja scr.ee on mõlemad lubatavad)
// Faili sees võib olla üks JSON objekt või mittu objekti massiivina.
// Pärast valideerimist tagastada aruanne iga objekti kohta, kas ja millised puudused esinesid.