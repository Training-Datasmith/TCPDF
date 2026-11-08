<?php

function tcpdf_unit_document($orientation, $unit, $format, $unicode, $setup)
{
    $pdf = new TCPDF($orientation, $unit, $format, $unicode);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->setCompression(false);
    call_user_func($setup, $pdf);
    return $pdf;
}

function test_document_portrait_hello()
{
    $pdf = tcpdf_unit_document('P', 'mm', 'A4', false, function ($pdf) {
        $pdf->AddPage();
        expect_near(210, $pdf->getPageWidth(), 0.05, 'page width');
        expect_near(297, $pdf->getPageHeight(), 0.05, 'page height');
        $pdf->setFont('helvetica', '', 12);
        $pdf->Cell(0, 10, 'Hello', 0, 1);
        expect_same(1, $pdf->getNumPages(), 'num pages');
        expect_same(1, $pdf->getPage(), 'current page');
    });
    $out = $pdf->Output('doc.pdf', 'S');
    expect_true(strpos($out, '%PDF-') === 0, 'pdf header');
    expect_true(substr(rtrim($out), -5) === '%%EOF', 'pdf eof');
    expect_true(strpos($out, 'endobj') !== false, 'pdf endobj');
    expect_true(strpos($out, '6.11.2') !== false, 'pdf version str');
    expect_true(strpos($out, 'Hello') !== false, 'pdf hello');
}

function test_document_landscape_and_custom()
{
    $land = tcpdf_unit_document('L', 'mm', 'A4', false, function ($pdf) {
        $pdf->AddPage();
        expect_near(297, $pdf->getPageWidth(), 0.05, 'land width');
        expect_near(210, $pdf->getPageHeight(), 0.05, 'land height');
        expect_true($pdf->getPageWidth() > $pdf->getPageHeight(), 'land orientation');
    });
    $land->Output('doc.pdf', 'S');
    // Numeric array [w,h] in mm with portrait swaps when first > second (fwPt > fhPt).
    $custom = tcpdf_unit_document('P', 'mm', array(100, 50), false, function ($pdf) {
        $pdf->AddPage();
        expect_near(50, $pdf->getPageWidth(), 0.05, 'custom width');
        expect_near(100, $pdf->getPageHeight(), 0.05, 'custom height');
    });
    $custom->Output('doc.pdf', 'S');
}

function test_document_multipage_and_break()
{
    $two = tcpdf_unit_document('P', 'mm', 'A4', false, function ($pdf) {
        $pdf->AddPage();
        $pdf->AddPage();
        expect_same(2, $pdf->getNumPages(), 'two pages count');
        expect_same(2, $pdf->getPage(), 'two pages current');
    });
    $two->Output('doc.pdf', 'S');
    $break = tcpdf_unit_document('P', 'mm', array(40, 30), false, function ($pdf) {
        $pdf->setMargins(2, 2, 2);
        $pdf->setAutoPageBreak(true, 2);
        $pdf->AddPage();
        $pdf->setFont('helvetica', '', 12);
        for ($i = 0; $i < 20; ++$i) {
            $pdf->Cell(0, 5, 'Line', 0, 1);
        }
        expect_true($pdf->getNumPages() > 1, 'autopagebreak');
    });
    $break->Output('doc.pdf', 'S');
}

function test_document_title_and_errors()
{
    $titled = tcpdf_unit_document('P', 'mm', 'A4', false, function ($pdf) {
        $pdf->setTitle('Unit Title');
        $pdf->AddPage();
        $pdf->setFont('helvetica', '', 12);
        $pdf->Cell(0, 10, 'T', 0, 1);
    });
    $out = $titled->Output('doc.pdf', 'S');
    expect_true(strpos($out, 'Unit Title') !== false, 'title in pdf');
    expect_exception('Incorrect unit: bogus', function () {
        new TCPDF('P', 'bogus', 'A4');
    });
    $pdf = tcpdf_unit_document('P', 'mm', 'A4', false, function ($pdf) {
        $pdf->AddPage();
    });
    expect_exception('Incorrect output destination: X', function () use ($pdf) {
        $pdf->Output('doc.pdf', 'X');
    });
}
