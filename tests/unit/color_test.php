<?php

function test_color_convert_html()
{
    $spot = array();
    $rgb = array('R' => 255, 'G' => 0, 'B' => 0);
    expect_same($rgb, TCPDF_COLORS::convertHTMLColorToDec('#ff0000', $spot), '#ff0000');
    expect_same($rgb, TCPDF_COLORS::convertHTMLColorToDec('#f00', $spot), '#f00');
    expect_same($rgb, TCPDF_COLORS::convertHTMLColorToDec('red', $spot), 'red name');
    expect_same(
        array('C' => 0, 'M' => 0, 'Y' => 0, 'K' => 0),
        TCPDF_COLORS::convertHTMLColorToDec('#00000000', $spot),
        'cmyk hex'
    );
    expect_same(array(255, 0, 0), TCPDF_COLORS::convertHTMLColorToDec('rgb(255, 0, 0)', $spot), 'rgb list');
    expect_same(array(255, 0, 0), TCPDF_COLORS::convertHTMLColorToDec('rgba(255,0,0,0.5)', $spot), 'rgba');
    expect_same(array(255, 0, 0), TCPDF_COLORS::convertHTMLColorToDec('rgb(100%,0%,0%)', $spot), 'rgb percent');
    expect_same(array(100, 0, 0, 0), TCPDF_COLORS::convertHTMLColorToDec('cmyk(100,0,0,0)', $spot), 'cmyk func');
    expect_same($rgb, TCPDF_COLORS::convertHTMLColorToDec("['rgb',1,0,0]", $spot), 'js rgb');
    expect_same(array(), TCPDF_COLORS::convertHTMLColorToDec('color.transparent', $spot), 'transparent');
    $def = array('R' => 128, 'G' => 128, 'B' => 128);
    expect_same($def, TCPDF_COLORS::convertHTMLColorToDec('', $spot), 'empty default');
    expect_same($def, TCPDF_COLORS::convertHTMLColorToDec('notacolor', $spot), 'unknown default');
}

function test_color_spot_and_strings()
{
    $spot = array();
    $sc = TCPDF_COLORS::getSpotColor('Cyan', $spot);
    expect_same(100, $sc['C'], 'spot C');
    expect_same(0, $sc['M'], 'spot M');
    expect_same(0, $sc['Y'], 'spot Y');
    expect_same(0, $sc['K'], 'spot K');
    expect_same('Cyan', $sc['name'], 'spot name');
    expect_same(1, $sc['i'], 'spot i');
    expect_same($sc, $spot['Cyan'], 'spot stored');
    $sc2 = TCPDF_COLORS::getSpotColor('Cyan', $spot);
    expect_same(1, $sc2['i'], 'spot i stable');
    expect_false(TCPDF_COLORS::getSpotColor('no-such-spot', $spot), 'spot missing');
    expect_same('[1.000000 0.000000 0.000000]', TCPDF_COLORS::getColorStringFromArray(array(255, 0, 0)), 'color str rgb');
    expect_same('[1.000000]', TCPDF_COLORS::getColorStringFromArray(array(255)), 'color str gray');
    expect_same(
        '[1.000000 0.000000 0.000000 0.000000]',
        TCPDF_COLORS::getColorStringFromArray(array(100, 0, 0, 0)),
        'color str cmyk'
    );
    expect_same("['RGB',1.000000,0.000000,0.000000]", TCPDF_COLORS::_JScolor('#ff0000'), 'jscolor hex');
    expect_same('color.red', TCPDF_COLORS::_JScolor('red'), 'jscolor red');
    expect_same('color.transparent', TCPDF_COLORS::_JScolor('nope'), 'jscolor bad');
}
