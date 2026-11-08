<?php

/**
 * Minimal assertions for TCPDF unit tests (PHP 7.1+).
 */

function unit_format_value($value)
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    if (is_null($value)) {
        return 'null';
    }
    if (is_array($value)) {
        return var_export($value, true);
    }
    if (is_string($value)) {
        if (strlen($value) > 200) {
            return substr(var_export($value, true), 0, 200) . '...';
        }
        return var_export($value, true);
    }
    return var_export($value, true);
}

function expect_same($expected, $actual, $message)
{
    if ($expected === $actual) {
        return true;
    }
  throw new Exception(
        $message . ' expected ' . unit_format_value($expected) . ' got ' . unit_format_value($actual)
    );
}

function expect_true($value, $message)
{
    if ($value === true) {
        return true;
    }
    throw new Exception($message . ' expected true got ' . unit_format_value($value));
}

function expect_false($value, $message)
{
    if ($value === false) {
        return true;
    }
    throw new Exception($message . ' expected false got ' . unit_format_value($value));
}

function expect_near($expected, $actual, $tolerance, $message)
{
    if (abs($expected - $actual) <= $tolerance) {
        return true;
    }
    throw new Exception(
        $message . ' expected ~' . $expected . ' got ' . $actual . ' (tolerance ' . $tolerance . ')'
    );
}

function expect_exception($substring, $callable)
{
    $threw = false;
    $msg = '';
    try {
        call_user_func($callable);
    } catch (Exception $e) {
        $threw = true;
        $msg = $e->getMessage();
    }
    if (!$threw) {
        throw new Exception('expected exception containing ' . var_export($substring, true));
    }
    if (strpos($msg, $substring) === false) {
        throw new Exception(
            'exception message must contain ' . var_export($substring, true) . ' got ' . unit_format_value($msg)
        );
    }
    return true;
}

function expect_array_components_eq($expected, $actual, $message)
{
    if (count($expected) !== count($actual)) {
        throw new Exception($message . ' length mismatch');
    }
    $i = 0;
    foreach ($expected as $v) {
        if (!isset($actual[$i]) || $actual[$i] != $v) {
            throw new Exception(
                $message . ' at index ' . $i . ' expected ' . unit_format_value($v)
                . ' got ' . unit_format_value(isset($actual[$i]) ? $actual[$i] : null)
            );
        }
        ++$i;
    }
    return true;
}
