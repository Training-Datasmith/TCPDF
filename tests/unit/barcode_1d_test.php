<?php

function test_barcode_1d_c39_and_ean()
{
    $bc = new TCPDFBarcode('Ab', 'C39');
    $arr = $bc->getBarcodeArray();
    expect_same('*AB*', $arr['code'], 'c39 code');
    expect_same(1, $arr['maxh'], 'c39 maxh');
    expect_true($arr['maxw'] > 0, 'c39 maxw');
    $sumw = 0;
    $hasBar = false;
    foreach ($arr['bcode'] as $el) {
        expect_true(array_key_exists('t', $el), 'bcode t');
        expect_true(array_key_exists('w', $el), 'bcode w');
        expect_true(array_key_exists('h', $el), 'bcode h');
        expect_true(array_key_exists('p', $el), 'bcode p');
        $sumw += $el['w'];
        if ($el['t'] === true) {
            $hasBar = true;
        }
    }
    expect_same($arr['maxw'], $sumw, 'c39 width sum');
    expect_true($hasBar, 'c39 has bar');
    $bcPlus = new TCPDFBarcode('A', 'C39+');
    expect_same('*AA*', $bcPlus->getBarcodeArray()['code'], 'c39+ checksum');
    expect_false((new TCPDFBarcode('!', 'C39'))->getBarcodeArray(), 'c39 invalid');
    $c128 = new TCPDFBarcode('1234', 'C128');
    $a128 = $c128->getBarcodeArray();
    expect_true(is_array($a128) && $a128 !== false, 'c128 array');
    expect_true(strlen($a128['code']) > 0, 'c128 code');
    expect_true(count($a128['bcode']) > 0, 'c128 bcode');
    $ean = new TCPDFBarcode('400638133393', 'EAN13');
    expect_same('4006381333931', $ean->getBarcodeArray()['code'], 'ean13 check');
    expect_false((new TCPDFBarcode('4006381333932', 'EAN13'))->getBarcodeArray(), 'ean13 bad check');
    expect_false((new TCPDFBarcode('1234', 'NOTATYPE'))->getBarcodeArray(), 'unknown 1d');
}

function test_barcode_1d_svg_html_png()
{
    $bc = new TCPDFBarcode('A', 'C39');
    $arr = $bc->getBarcodeArray();
    $svg = $bc->getBarcodeSVGcode(2, 30, 'black');
    expect_true(strpos($svg, '<?xml version="1.0" standalone="no"?>') === 0, 'svg header');
    expect_true(strpos($svg, '<desc>*A*</desc>') !== false, 'svg desc');
    expect_true(strpos($svg, 'fill="black"') !== false, 'svg fill');
    $barCount = 0;
    foreach ($arr['bcode'] as $el) {
        if ($el['t'] === true) {
            ++$barCount;
        }
    }
    expect_same($barCount, substr_count($svg, '<rect'), 'svg rect count');
    $html = $bc->getBarcodeHTML(2, 30, 'black');
    expect_true(strpos($html, '<div ') === 0, 'html div');
    expect_true(strpos($html, 'background-color:black') !== false, 'html color');
    expect_true(strpos($html, '<svg') === false, 'html no svg');
    $png = $bc->getBarcodePngData(2, 30, array(0, 0, 0));
    if (function_exists('imagecreate')) {
        expect_same("\x89PNG\r\n\x1a\n", substr($png, 0, 8), 'png magic');
    } elseif (extension_loaded('imagick')) {
        expect_true($png instanceof Imagick, 'imagick png');
    } else {
        expect_false($png, 'no gd imagick');
    }
}
