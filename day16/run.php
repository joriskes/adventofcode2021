<?php
require __DIR__ . '/../bootstrap.php';

$input = file_get_contents(__DIR__ . '/input.txt');

function align_to_hex($int)
{
    return ceil($int / 4) * 4;
}

function decode_packet($bin, &$pointer, $depth = 0)
{
    $res = [];
    $res['version'] = bindec(substr($bin, $pointer, 3));
    $pointer += 3;
    $res['type'] = bindec(substr($bin, $pointer, 3));
    $pointer += 3;
    switch ($res['type']) {
        case 4:
            // Literal value packet
            $res['data_bin'] = '';
            $keep_reading = true;
            while ($keep_reading) {
                $subdata = substr($bin, $pointer, 5);
                $pointer += 5;
                if ($subdata[0] == '0') {
                    $keep_reading = false;
                }
                $res['data_bin'] .= substr($subdata, 1);
            }
            $res['data_dec'] = bindec($res['data_bin']);
            break;
        default:
            // Operator packet
            $res['length_type'] = bindec('0' . substr($bin, $pointer, 1));
            $pointer += 1;
            if ($res['length_type'] == 0) {
                // If the length type ID is 0, then the next 15 bits are a number that represents the total length in bits of the sub-packets contained by this packet.
                $res['length_bin'] = substr($bin, $pointer, 15);
                $pointer += 15;
                $res['length_dec'] = bindec($res['length_bin']);
                $start = $pointer;
                $res['subpackets'] = [];
                while ($pointer - $start < $res['length_dec']) {
                    $subpacket = decode_packet($bin, $pointer, $depth + 1);
                    array_push($res['subpackets'], $subpacket);
                }
            } else {
                // If the length type ID is 1, then the next 11 bits are a number that represents the number of sub-packets immediately contained by this packet.
                $res['length_bin'] = substr($bin, $pointer, 11);
                $pointer += 11;
                $res['subpacket_count'] = bindec($res['length_bin']);
                // If the length type ID is 0, then the next 15 bits are a number that represents the total length in bits of the sub-packets contained by this packet.
                $count = 0;
                $res['subpackets'] = [];
                while ($count < $res['subpacket_count']) {
                    $subpacket = decode_packet($bin, $pointer, $depth + 1);
                    array_push($res['subpackets'], $subpacket);
                    $count++;
                }
            }
            break;
    }
    return $res;
}

function hex2bin_proper($str)
{
    $chars = str_split($str);
    $res = '';
    foreach ($chars as $char) {
        $bin = decbin(hexdec($char));
        while (strlen($bin) < 4) {
            $bin = '0' . $bin;
        }
        $res .= $bin;
    }
    return $res;
}

function run_operators($packet, $depth = 0)
{
    $res = 0;
    $values = [];
    if (isset($packet['subpackets'])) {
        foreach ($packet['subpackets'] as $p) {
            $values[] = run_operators($p, $depth + 1);
        }
    }
    switch ($packet['type']) {
        // Packets with type ID 0 are sum packets - their value is the sum of the values of their sub-packets.
        // If they only have a single sub-packet, their value is the value of the sub-packet.
        case 0:
            foreach ($values as $value) {
                $res = $res + $value;
            }
            break;
        // Packets with type ID 1 are product packets - their value is the result of multiplying together the values
        // of their sub-packets. If they only have a single sub-packet, their value is the value of the sub-packet.
        case 1:
            $res = array_shift($values);
            foreach ($values as $value) {
                $res = $res * $value;
            }
            break;
        // Packets with type ID 2 are minimum packets - their value is the minimum of the values of their sub-packets.
        case 2:
            $res = min($values);
            break;
        // Packets with type ID 3 are maximum packets - their value is the maximum of the values of their sub-packets.
        case 3:
            $res = max($values);
            break;
        // Literal values (type ID 4) represent a single number
        case 4:
            $res = $packet['data_dec'];
            break;
        // Packets with type ID 5 are greater than packets - their value is 1 if the value of the first sub-packet
        // is greater than the value of the second sub-packet; otherwise, their value is 0. These packets always have
        // exactly two sub-packets.
        case 5:
            if ($values[0] > $values[1]) {
                $res = 1;
            } else {
                $res = 0;
            }
            break;
        // Packets with type ID 6 are less than packets - their value is 1 if the value of the first sub-packet is
        // less than the value of the second sub-packet; otherwise, their value is 0. These packets always have exactly
        // two sub-packets.
        case 6:
            if ($values[0] < $values[1]) {
                $res = 1;
            } else {
                $res = 0;
            }
            break;
        // Packets with type ID 7 are equal to packets - their value is 1 if the value of the first sub-packet is
        // equal to the value of the second sub-packet; otherwise, their value is 0. These packets always have exactly
        // two sub-packets.
        case 7:
            if ($values[0] == $values[1]) {
                $res = 1;
            } else {
                $res = 0;
            }
            break;
    }
    return $res;
}

function sum_version($packet)
{
    $sum = 0;
    if (isset($packet['subpackets'])) {
        foreach ($packet['subpackets'] as $p) {
            $sum += sum_version($p);
        }
    }
    $sum += $packet['version'];
    return $sum;
}

$pointer = 0;
$bin = hex2bin_proper($input);
$packet = decode_packet($bin, $pointer);
$part1 = sum_version($packet);
p('Part 1: ' . $part1); // 1596 too high

$part2 = run_operators($packet);
p('Part 2: ' . $part2);
