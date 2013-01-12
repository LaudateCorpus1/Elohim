#!/usr/local/bin/php

<?php
$response = file_get_contents('http://www.islamine.com/index/sitemap');

$_doc = new DOMDocument('1.0', 'UTF-8');
$_doc->formatOutput = true;

$_staticURL = json_decode($response);

$_path = '../../www/sitemap.xml';

$root = $_doc->createElement('urlset');
$_doc->appendChild($root);
$_doc->createAttributeNS("http://www.sitemaps.org/schemas/sitemap/0.9", 'xmlns');

$attribute = $_doc->createAttribute('xmlns:xsi');
$attribute->value = "http://www.w3.org/2001/XMLSchema-instance";
$root->appendChild($attribute);
$attribute = $_doc->createAttribute('xsi:schemaLocation');
$attribute->value = "http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd";
$root->appendChild($attribute);

foreach($_staticURL as $staticURL)
{
    $url = $_doc->createElement('url');

    $loc = $_doc->createElement('loc');
    $loc->appendChild($_doc->createTextNode($staticURL->loc));
    $url->appendChild($loc);

    $priority = $_doc->createElement('priority');
    $priority->appendChild($_doc->createTextNode($staticURL->priority));
    $url->appendChild($priority);

    $freq = $_doc->createElement('changefreq');
    $freq->appendChild($_doc->createTextNode($staticURL->changefreq));
    $url->appendChild($freq);

    $root->appendChild($url);
}

$res = $_doc->save($_path);
