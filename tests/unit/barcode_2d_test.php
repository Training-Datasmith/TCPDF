<?php

function test_barcode_2d_test_and_raw()
{
    $bc = new TCPDF2DBarcode('x', 'TEST');
    $arr = $bc->getBarcodeArray();
    expect_same(5, $arr['num_rows'], 'test rows');
    expect_same(15, $arr['num_cols'], 'test cols');
    expect_same(array(1, 1, 1, 0, 1, 1, 1, 0, 1, 1, 1, 0, 1, 1, 1), $arr['bcode'][0], 'test row0');
    expect_same('x', $arr['code'], 'test code');
    $raw = new TCPDF2DBarcode('101,010', 'RAW');
    $rawArr = $raw->getBarcodeArray();
    expect_same(2, $rawArr['num_rows'], 'raw rows');
    expect_same(3, $rawArr['num_cols'], 'raw cols');
    expect_same(array(array('1', '0', '1'), array('0', '1', '0')), $rawArr['bcode'], 'raw bcode');
    expect_same('101,010', $rawArr['code'], 'raw code');
    $raw2 = new TCPDF2DBarcode('[101][010]', 'RAW2');
    $raw2Arr = $raw2->getBarcodeArray();
    expect_same(2, $raw2Arr['num_rows'], 'raw2 rows');
    expect_same(3, $raw2Arr['num_cols'], 'raw2 cols');
    expect_same($rawArr['bcode'], $raw2Arr['bcode'], 'raw2 bcode');
    expect_same('101][010', $raw2Arr['code'], 'raw2 code');
}

function test_barcode_2d_qr_datamatrix_and_unknown()
{
    foreach (array('QRCODE,L', 'QRCODE,H') as $type) {
        $qr = new TCPDF2DBarcode('TCPDF', $type);
        $arr = $qr->getBarcodeArray();
        expect_same('TCPDF', $arr['code'], $type . ' code');
        expect_same($arr['num_rows'], $arr['num_cols'], $type . ' square');
        expect_true($arr['num_rows'] >= 21, $type . ' size');
        foreach ($arr['bcode'] as $row) {
            expect_same($arr['num_cols'], count($row), $type . ' row len');
            foreach ($row as $cell) {
                expect_true($cell == 0 || $cell == 1, $type . ' cell');
            }
        }
    }
    foreach (array('DATAMATRIX', 'PDF417') as $type) {
        $bc = new TCPDF2DBarcode('TCPDF', $type);
        $arr = $bc->getBarcodeArray();
        expect_same('TCPDF', $arr['code'], $type . ' code');
        expect_true($arr['num_rows'] >= 1, $type . ' rows');
        expect_true($arr['num_cols'] >= 1, $type . ' cols');
        expect_true(count($arr['bcode']) == $arr['num_rows'], $type . ' bcode rows');
    }
    expect_same(array(), (new TCPDF2DBarcode('x', 'NOTATYPE'))->getBarcodeArray(), 'unknown 2d');
    $testBc = new TCPDF2DBarcode('x', 'TEST');
    $svg = $testBc->getBarcodeSVGcode(3, 3, 'black');
    expect_true(strpos($svg, '<svg') !== false, '2d svg');
    expect_true(strpos($svg, '<rect') !== false, '2d svg rect');
    $html = $testBc->getBarcodeHTML(10, 10, 'black');
    expect_true(strpos($html, '<div ') === 0, '2d html');
}
