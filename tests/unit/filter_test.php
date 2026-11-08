<?php

function test_filter_available_and_hex()
{
    expect_same(
        array('ASCIIHexDecode', 'ASCII85Decode', 'LZWDecode', 'FlateDecode', 'RunLengthDecode'),
        TCPDF_FILTERS::getAvailableFilters(),
        'available filters'
    );
    expect_same('AB', TCPDF_FILTERS::decodeFilter('ASCIIHexDecode', '4142>'), 'hex ab');
    expect_same('AB', TCPDF_FILTERS::decodeFilter('ASCIIHexDecode', "41 42>"), 'hex spaced');
    expect_same('@', TCPDF_FILTERS::decodeFilter('ASCIIHexDecode', '4>'), 'hex odd nibble with EOD');
    expect_exception(
        'decodeFilterASCIIHexDecode: invalid code',
        function () {
            TCPDF_FILTERS::decodeFilter('ASCIIHexDecode', '4G>');
        }
    );
}

function test_filter_runlength_and_misc()
{
    expect_same(
        'A',
        TCPDF_FILTERS::decodeFilterRunLengthDecode(chr(0) . 'A' . chr(128)),
        'rle literal'
    );
    expect_same(
        'BB',
        TCPDF_FILTERS::decodeFilterRunLengthDecode(chr(255) . 'B' . chr(128)),
        'rle repeat'
    );
    expect_same('abc', TCPDF_FILTERS::decodeFilter('NoSuchFilter', 'abc'), 'unknown filter');
    expect_exception(
        'not been yet implemented',
        function () {
            TCPDF_FILTERS::decodeFilter('CCITTFaxDecode', 'x');
        }
    );
    if (function_exists('gzcompress') && function_exists('gzuncompress')) {
        expect_same(
            'hello',
            TCPDF_FILTERS::decodeFilterFlateDecode(gzcompress('hello')),
            'flate roundtrip'
        );
        expect_exception(
            'decodeFilterFlateDecode: invalid code',
            function () {
                TCPDF_FILTERS::decodeFilterFlateDecode('not-flate');
            }
        );
    } else {
        echo "SKIP flate (zlib missing)\n";
    }
}
