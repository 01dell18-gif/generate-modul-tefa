<?php
// api/export-docx.php

$htmlContent = $_POST['html'] ?? '';
$judul       = $_POST['judul'] ?? 'RPP-GEMA-FYJ';

// If sent as JSON
if (empty($htmlContent)) {
    $raw = file_get_contents('php://input');
    $json = json_decode($raw, true);
    if (!empty($json['html'])) {
        $htmlContent = $json['html'];
        $judul = $json['judul'] ?? $judul;
    }
}

if (empty($htmlContent)) {
    http_response_code(400);
    die("Konten dokumen tidak ditemukan untuk diekspor.");
}

$safeFilename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', trim($judul));
if (empty($safeFilename)) {
    $safeFilename = 'RPP-TEFA-GEMA-FYJ';
}
$filename = $safeFilename . '.doc';

$wordHeader = '<!DOCTYPE html>
<html xmlns:o="urn:schemas-microsoft-com:office:office" 
      xmlns:w="urn:schemas-microsoft-com:office:word" 
      xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta charset="utf-8">
<title>' . htmlspecialchars($judul, ENT_QUOTES, 'UTF-8') . '</title>
<!--[if gte mso 9]>
<xml>
 <w:WordDocument>
  <w:View>Print</w:View>
  <w:Zoom>100</w:Zoom>
  <w:DoNotOptimizeForBrowser/>
 </w:WordDocument>
</xml>
<![endif]-->
<style>
@page Section1 {
    size: 21.0cm 29.7cm; /* Ukuran Kertas A4 */
    margin: 2.54cm 2.54cm 2.54cm 2.54cm; /* Margin 1 inci sekeliling */
    mso-header-margin: 1.25cm;
    mso-footer-margin: 1.25cm;
    mso-paper-source: 0;
}
div.Section1 {
    page: Section1;
}
body {
    font-family: "Times New Roman", Times, serif;
    font-size: 11pt;
    line-height: 1.35;
    color: #000000;
}
h1 { 
    font-size: 12.5pt; 
    text-align: center; 
    margin: 3pt 0; 
    text-transform: uppercase; 
    font-weight: bold; 
}
h2 { 
    font-size: 11.5pt; 
    margin: 14pt 0 4pt; 
    border-bottom: 1.5pt solid #000; 
    padding-bottom: 2pt; 
    font-weight: bold; 
    text-transform: uppercase;
}
h3 { 
    font-size: 11pt; 
    margin: 10pt 0 3pt; 
    font-weight: bold; 
}
p, li { 
    font-size: 11pt; 
    text-align: justify; 
    margin: 0 0 5pt; 
}
ul, ol {
    margin: 3pt 0 7pt 18pt;
    padding: 0;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin: 6pt 0 10pt;
    font-size: 10.5pt;
    mso-table-lspace: 0pt;
    mso-table-rspace: 0pt;
}
th, td {
    border: 1px solid #000000;
    padding: 4.5pt 6pt;
    vertical-align: top;
}
th {
    background-color: #f2f2f2;
    font-weight: bold;
}
table.ttd {
    margin-top: 24pt;
    border: none;
    width: 100%;
}
table.ttd td {
    border: none;
    text-align: center;
    width: 50%;
    padding: 0;
}
.page-break {
    page-break-before: always;
    mso-special-character: line-break;
}
</style>
</head>
<body>
<div class="Section1">';

$wordFooter = '</div></body></html>';

header('Content-Type: application/vnd.ms-word; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0, no-cache, no-store, must-revalidate');
header('Pragma: public');

echo $wordHeader . $htmlContent . $wordFooter;
exit;
