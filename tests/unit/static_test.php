<?php

function test_static_version_and_producer()
{
    expect_same('6.11.2', TCPDF_STATIC::getTCPDFVersion(), 'version');
    $root = dirname(__DIR__, 2);
    expect_same(trim(file_get_contents($root . '/VERSION')), TCPDF_STATIC::getTCPDFVersion(), 'VERSION file');
    $producer = TCPDF_STATIC::getTCPDFProducer();
    expect_true(strpos($producer, '6.11.2') !== false, 'producer version');
    expect_true(strpos($producer, 'tcpdf.org') !== false, 'producer url');
}

function test_static_empty_and_escape()
{
    expect_true(TCPDF_STATIC::empty_string(null), 'empty null');
    expect_true(TCPDF_STATIC::empty_string(''), 'empty string');
    expect_false(TCPDF_STATIC::empty_string('0'), 'not empty 0 string');
    expect_false(TCPDF_STATIC::empty_string(0), 'not empty int 0');
    expect_same('\\(a\\)', TCPDF_STATIC::_escape('(a)'), 'escape parens');
    expect_same('a\\\\b', TCPDF_STATIC::_escape("a\\b"), 'escape backslash');
    expect_same("a\\rb", TCPDF_STATIC::_escape("a\rb"), 'escape cr');
    expect_same('a&lt;b&gt;&amp;c', TCPDF_STATIC::_escapeXML('a<b>&c'), 'escape xml');
    expect_same('', TCPDF_STATIC::_escapeXML(null), 'escape xml null');
    expect_same('ab', TCPDF_STATIC::_escapeXML("a\0b"), 'escape xml null byte');
    expect_same('ab', TCPDF_STATIC::removeSHY("a\xC2\xADb", true), 'remove shy utf8');
    expect_same('ab', TCPDF_STATIC::removeSHY("a\xADb", false), 'remove shy byte');
    expect_same("a\xADb", TCPDF_STATIC::removeSHY("a\xADb", true), 'shy unicode mode');
}

function test_static_names_hex_roman()
{
    expect_same('Hello', TCPDF_STATIC::encodeNameObject('Hello'), 'name hello');
    expect_same('A#20B', TCPDF_STATIC::encodeNameObject('A B'), 'name space');
    expect_same('Name#', TCPDF_STATIC::encodeNameObject('Name#'), 'name hash');
    expect_same('AB', TCPDF_STATIC::convertHexStringToString('4142'), 'hex ab');
    expect_same("A@", TCPDF_STATIC::convertHexStringToString('414'), 'hex odd');
    expect_same('4142', TCPDF_STATIC::convertStringToHexString('AB'), 'to hex');
    expect_same(
        'AB',
        TCPDF_STATIC::convertHexStringToString(TCPDF_STATIC::convertStringToHexString('AB')),
        'hex roundtrip'
    );
    expect_same(2, TCPDF_STATIC::revstrpos('abcdef', 'cd', 0), 'revstrpos cd');
    expect_same(3, TCPDF_STATIC::revstrpos('abcabc', 'ab', 0), 'revstrpos ab');
    expect_same(false, TCPDF_STATIC::revstrpos('abc', 'z', 0), 'revstrpos miss');
    expect_same('I', TCPDF_STATIC::intToRoman(1), 'roman 1');
    expect_same('IV', TCPDF_STATIC::intToRoman(4), 'roman 4');
    expect_same('IX', TCPDF_STATIC::intToRoman(9), 'roman 9');
    expect_same('XL', TCPDF_STATIC::intToRoman(40), 'roman 40');
    expect_same('XLIX', TCPDF_STATIC::intToRoman(49), 'roman 49');
    expect_same('MCMXCIV', TCPDF_STATIC::intToRoman(1994), 'roman 1994');
    expect_same('MMMCMXCIX', TCPDF_STATIC::intToRoman(3999), 'roman 3999');
    expect_same('4000', TCPDF_STATIC::intToRoman(4000), 'roman 4000');
    expect_same('', TCPDF_STATIC::intToRoman(0), 'roman 0');
    expect_same('1.000', TCPDF_STATIC::formatPageNumber(1000), 'format page 1000');
    expect_same('12', TCPDF_STATIC::formatPageNumber(12), 'format page 12');
    expect_same('1.000', TCPDF_STATIC::formatTOCPageNumber(1000), 'format toc 1000');
}

function test_static_page_geometry()
{
    expect_same(array(595.276, 841.890), TCPDF_STATIC::getPageSizeFromFormat('A4'), 'A4');
    expect_same(array(841.890, 1190.551), TCPDF_STATIC::getPageSizeFromFormat('A3'), 'A3');
    expect_same(array(419.528, 595.276), TCPDF_STATIC::getPageSizeFromFormat('A5'), 'A5');
    expect_same(array(612.000, 792.000), TCPDF_STATIC::getPageSizeFromFormat('LETTER'), 'LETTER');
    expect_same(
        TCPDF_STATIC::getPageSizeFromFormat('A4'),
        TCPDF_STATIC::getPageSizeFromFormat('not-a-format'),
        'unknown format'
    );
    expect_same('SinglePage', TCPDF_STATIC::getPageLayoutMode('default'), 'layout default');
    expect_same('SinglePage', TCPDF_STATIC::getPageLayoutMode('single'), 'layout single');
    expect_same('SinglePage', TCPDF_STATIC::getPageLayoutMode('SinglePage'), 'layout SinglePage');
    expect_same('OneColumn', TCPDF_STATIC::getPageLayoutMode('continuous'), 'layout continuous');
    expect_same('OneColumn', TCPDF_STATIC::getPageLayoutMode('OneColumn'), 'layout OneColumn');
    expect_same('TwoColumnLeft', TCPDF_STATIC::getPageLayoutMode('two'), 'layout two');
    expect_same('TwoColumnLeft', TCPDF_STATIC::getPageLayoutMode('TwoColumnLeft'), 'layout TwoColumnLeft');
    expect_same('TwoColumnRight', TCPDF_STATIC::getPageLayoutMode('TwoColumnRight'), 'layout TwoColumnRight');
    expect_same('TwoPageLeft', TCPDF_STATIC::getPageLayoutMode('TwoPageLeft'), 'layout TwoPageLeft');
    expect_same('TwoPageRight', TCPDF_STATIC::getPageLayoutMode('TwoPageRight'), 'layout TwoPageRight');
    expect_same('SinglePage', TCPDF_STATIC::getPageLayoutMode('nope'), 'layout nope');
    expect_same('UseNone', TCPDF_STATIC::getPageMode('UseNone'), 'mode UseNone');
    expect_same('UseOutlines', TCPDF_STATIC::getPageMode('UseOutlines'), 'mode UseOutlines');
    expect_same('UseThumbs', TCPDF_STATIC::getPageMode('UseThumbs'), 'mode UseThumbs');
    expect_same('FullScreen', TCPDF_STATIC::getPageMode('FullScreen'), 'mode FullScreen');
    expect_same('UseOC', TCPDF_STATIC::getPageMode('UseOC'), 'mode UseOC');
    expect_same('UseNone', TCPDF_STATIC::getPageMode('nope'), 'mode nope');
    // Documented quirk: empty string maps to UseAttachments branch; literal UseAttachments is default.
    expect_same('UseAttachments', TCPDF_STATIC::getPageMode(''), 'mode empty quirk');
    expect_same('UseNone', TCPDF_STATIC::getPageMode('UseAttachments'), 'mode UseAttachments literal');
}

function test_static_page_boxes_and_borders()
{
    $dim = TCPDF_STATIC::setPageBoxes(1, 'MediaBox', 0, 0, 100, 200, true, 2.5);
    expect_same(0, $dim[1]['MediaBox']['llx'], 'mbox llx');
    expect_same(0, $dim[1]['MediaBox']['lly'], 'mbox lly');
    expect_same(100, $dim[1]['MediaBox']['urx'], 'mbox urx');
    expect_same(200, $dim[1]['MediaBox']['ury'], 'mbox ury');
    expect_same(null, TCPDF_STATIC::setPageBoxes(1, 'NotABox', 0, 0, 1, 1, true, 1), 'bad box');
    $swapped = TCPDF_STATIC::swapPageBoxCoordinates(1, $dim);
    expect_same($dim[1]['MediaBox']['lly'], $swapped[1]['MediaBox']['llx'], 'swap llx');
    expect_same($dim[1]['MediaBox']['llx'], $swapped[1]['MediaBox']['lly'], 'swap lly');
    expect_same($dim[1]['MediaBox']['ury'], $swapped[1]['MediaBox']['urx'], 'swap urx');
    expect_same($dim[1]['MediaBox']['urx'], $swapped[1]['MediaBox']['ury'], 'swap ury');
    expect_same(0, TCPDF_STATIC::getBorderMode(0, 'start', true), 'border 0');
    expect_same('', TCPDF_STATIC::getBorderMode('', 'start', true), 'border empty');
    $brd = TCPDF_STATIC::getBorderMode(1, 'start', true);
    expect_true(is_array($brd), 'border array');
    expect_true(array_key_exists('L', $brd), 'border L');
    expect_true(array_key_exists('T', $brd), 'border T');
    expect_true(array_key_exists('R', $brd), 'border R');
    expect_false(array_key_exists('B', $brd), 'border no B');
}

function test_static_binary_readers()
{
    expect_same(0x01020304, TCPDF_STATIC::_getULONG(pack('N', 0x01020304), 0), 'ulong');
    expect_same(0x0102, TCPDF_STATIC::_getUSHORT(pack('n', 0x0102), 0), 'ushort');
    expect_same(65, TCPDF_STATIC::_getBYTE(pack('C', 65), 0), 'byte');
    expect_same(-1, TCPDF_STATIC::_getFWORD(pack('n', 0xFFFF), 0), 'fword');
}

function test_static_encryption_and_misc()
{
    expect_same(2147422012, TCPDF_STATIC::getUserPermissionCode(array(), 0), 'perm empty');
    expect_same(2147422008, TCPDF_STATIC::getUserPermissionCode(array('print'), 0), 'perm print');
    expect_same(2147422014, TCPDF_STATIC::getUserPermissionCode(array('owner'), 0), 'perm owner');
    expect_same(2147422012, TCPDF_STATIC::getUserPermissionCode(array('print-high'), 0), 'perm high m0');
    expect_same(2147419964, TCPDF_STATIC::getUserPermissionCode(array('print-high'), 1), 'perm high m1');
    expect_same(2147422012, TCPDF_STATIC::getUserPermissionCode(array('not-a-permission'), 0), 'perm bad');
    expect_same("\x01\x00\x00\x00", TCPDF_STATIC::getEncPermissionsString(1), 'enc perm str');
    expect_same(md5('a', true), TCPDF_STATIC::_md5_16('a'), 'md5_16');
    $k = '';
    $c = '';
    $out1 = TCPDF_STATIC::_RC4('secret', 'hello', $k, $c);
    $k2 = '';
    $c2 = '';
    $out2 = TCPDF_STATIC::_RC4('secret', 'hello', $k2, $c2);
    expect_same($out1, $out2, 'rc4 deterministic fresh');
    expect_true($out1 !== 'hello', 'rc4 not plaintext');
    expect_same('S', TCPDF_STATIC::getPathPaintOperator('S'), 'path S');
    expect_same('S', TCPDF_STATIC::getPathPaintOperator('D'), 'path D');
    expect_same('f', TCPDF_STATIC::getPathPaintOperator('f'), 'path f');
    expect_same('f', TCPDF_STATIC::getPathPaintOperator('F'), 'path F');
    expect_same('B', TCPDF_STATIC::getPathPaintOperator('B'), 'path B');
    expect_same('B', TCPDF_STATIC::getPathPaintOperator('FD'), 'path FD');
    expect_same('B', TCPDF_STATIC::getPathPaintOperator('DF'), 'path DF');
    expect_same('W n', TCPDF_STATIC::getPathPaintOperator('CNZ'), 'path CNZ');
    expect_same('W* n', TCPDF_STATIC::getPathPaintOperator('CEO'), 'path CEO');
    expect_same('n', TCPDF_STATIC::getPathPaintOperator('n'), 'path n');
    expect_same('S', TCPDF_STATIC::getPathPaintOperator('nope', 'S'), 'path default S');
    expect_same('', TCPDF_STATIC::getPathPaintOperator('nope', ''), 'path default empty');
    expect_same(
        array(1, 0, 0, 1, 0, 0),
        TCPDF_STATIC::getTransformationMatrixProduct(array(1, 0, 0, 1, 0, 0), array(1, 0, 0, 1, 0, 0)),
        'matrix identity'
    );
    expect_array_components_eq(
        array(1, 0, 0, 1, 10, 20),
        TCPDF_STATIC::getSVGTransformMatrix('translate(10,20)'),
        'svg translate'
    );
    $scale = TCPDF_STATIC::getSVGTransformMatrix('scale(2)');
    expect_same(2, $scale[0] + 0, 'scale2 a');
    expect_same(2, $scale[3] + 0, 'scale2 d');
    $scale23 = TCPDF_STATIC::getSVGTransformMatrix('scale(2,3)');
    expect_same(2, $scale23[0] + 0, 'scale23 a');
    expect_same(3, $scale23[3] + 0, 'scale23 d');
    expect_same(
        array(1, 0, 0, 1, 0, 0),
        TCPDF_STATIC::getSVGTransformMatrix(''),
        'svg empty'
    );
    expect_near(0, TCPDF_STATIC::getVectorsAngle(1, 0, 1, 0), 1e-9, 'vectors same');
    expect_near(M_PI / 2, TCPDF_STATIC::getVectorsAngle(1, 0, 0, 1), 1e-9, 'vectors perp');
    $replaced = TCPDF_STATIC::replacePageNumAliases(
        'X {:pnp:} Y',
        array(array('3', 7, 1, array('{:pnp:}'))),
        0
    );
    expect_same('X 3 Y', $replaced[0], 'alias replace page');
    expect_same(-6, $replaced[1], 'alias diff');
    expect_same(0, TCPDF_STATIC::getTimestamp('D:1970-01-01 00:00:00'), 'timestamp epoch');
    expect_same("19700101000000+00'00'", TCPDF_STATIC::getFormattedDate(0), 'formatted date 0');
    $r1 = TCPDF_STATIC::getRandomSeed('seed');
    $r2 = TCPDF_STATIC::getRandomSeed('seed');
    expect_true(strlen($r1) > 0, 'random seed len');
    expect_true(strpos($r1, 'seed') !== false, 'random seed contains');
    expect_true($r1 !== $r2, 'random seed unique');
}
