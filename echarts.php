<?php

$countries = json_decode(file_get_contents('./dist/countries.json'), true);
$c_map = [];
foreach ($countries as $v) {
    $c_map[$v['cca2']] = $v['name']['common'];
}
unset($countries);

$features = [];
foreach(glob("./data/*.geo.json") as $filename) {
    $json = json_decode(file_get_contents($filename), true);

    if (empty($json['features'][0]['geometry']) || empty($json['features'][0]['properties'])) {
        //echo $filename, PHP_EOL;
        //print_r($json);
        continue;
    }

    $geometry = $json['features'][0]['geometry'];
    $properties = $json['features'][0]['properties'];
    $coordinatesNum = count($geometry['coordinates']);
    $properties['childNum'] = $coordinatesNum;
    $properties['cca2'] = strtoupper($properties['cca2']);
    if (empty($c_map[$properties['cca2']])) {
        continue;
    }
    $properties['name'] = $c_map[$properties['cca2']];

    $feature = [
        'geometry' => $geometry,
        'properties' => $properties,
    ];
    $features[] = $feature;
}

$world_features = json_encode([
    'type' => 'FeatureCollection',
    'features' => $features,
], JSON_UNESCAPED_UNICODE);

$js = <<<JS
(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['exports', 'echarts'], factory);
    } else if (typeof exports === 'object' && typeof exports.nodeName !== 'string') {
        // CommonJS
        factory(exports, require('echarts'));
    } else {
        // Browser globals
        factory({}, root.echarts);
    }
}(this, function (exports, echarts) {
    var log = function (msg) {
        if (typeof console !== 'undefined') {
            console && console.error && console.error(msg);
        }
    }
    if (!echarts) {
        log('ECharts is not Loaded');
        return;
    }
    if (!echarts.registerMap) {
        log('ECharts Map is not loaded')
        return;
    }
    echarts.registerMap('world-features', ${world_features});
}));
JS;
file_put_contents('./dist/world-features.js', $js);
