<?php

function test_font_unicode_and_sizes()
{
    expect_same('A', TCPDF_FONTS::unichr(65), 'unichr A');
    expect_same("\xC3\xA9", TCPDF_FONTS::unichr(0xE9), 'unichr eacute');
    expect_same(4, strlen(TCPDF_FONTS::unichr(0x1F600)), 'unichr emoji len');
    expect_same('', TCPDF_FONTS::unichr(0x110000), 'unichr oob');
    expect_same(chr(0xE9), TCPDF_FONTS::unichr(0xE9, false), 'unichr ascii mode');
    expect_same('A', TCPDF_FONTS::unichrASCII(65), 'unichrASCII');
    expect_same('A', TCPDF_FONTS::unichrUnicode(65), 'unichrUnicode');
    expect_same(8, TCPDF_FONTS::getFontRefSize('xx-small', 12), 'xx-small');
    expect_same(9, TCPDF_FONTS::getFontRefSize('x-small', 12), 'x-small');
    expect_same(10, TCPDF_FONTS::getFontRefSize('small', 12), 'small');
    expect_same(12, TCPDF_FONTS::getFontRefSize('medium', 12), 'medium');
    expect_same(14, TCPDF_FONTS::getFontRefSize('large', 12), 'large');
    expect_same(16, TCPDF_FONTS::getFontRefSize('x-large', 12), 'x-large');
    expect_same(18, TCPDF_FONTS::getFontRefSize('xx-large', 12), 'xx-large');
    expect_same(9, TCPDF_FONTS::getFontRefSize('smaller', 12), 'smaller');
    expect_same(15, TCPDF_FONTS::getFontRefSize('larger', 12), 'larger');
    expect_same(14, TCPDF_FONTS::getFontRefSize(14, 12), 'numeric size');
}
