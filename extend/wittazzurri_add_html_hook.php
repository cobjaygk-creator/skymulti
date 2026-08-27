<?php if (!defined('_GNUBOARD_')) exit;

add_event('html_purifier_config', function($config) {
    $add_html_tag = $config -> getHTMLDefinition(1);
    $add_html_tag -> addElement('link', 'Block', 'Flow', 'Common', ['rel' => 'Text', 'href' => 'Text']);
    $add_html_tag -> addElement('div', 'Block', 'Flow', 'Common', ['id' => 'Text', 'style' => 'Text']);
    $add_html_tag -> addElement('span', 'Block', 'Flow', 'Common', ['id' => 'Text','style' => 'Text']);
    $add_html_tag -> addElement('iframe', 'Block', 'Flow', 'Common', ['id' => 'Text', 'style' => 'Text', 'allow' => 'Text', 'allowfullscreen' => 'Bool']);
    $add_html_tag -> addElement('video', 'Block', 'Flow', 'Common', ['id' => 'Text', 'src' => 'Text', 'autoplay' => 'Bool', 'loop' => 'Bool', 'controls' => 'Bool', 'muted' => 'Bool', 'controlslist' => 'Text']);
    $add_html_tag -> addElement('audio', 'Block', 'Flow', 'Common', ['id' => 'Text', 'src' => 'Text', 'autoplay' => 'Bool', 'loop' => 'Bool', 'controls' => 'Bool', 'muted' => 'Bool', 'controlslist' => 'Text']);
}, 1, 1);


add_event('html_purifier_config', function($config) {
    $def = $config->getHTMLDefinition(true);

    // 공통 속성 정의
    $commonAttrs = ['id' => 'Text', 'class' => 'Text', 'style' => 'Text'];

    // div
    $def->addElement('div', 'Block', 'Flow', 'Common', array_merge($commonAttrs, ['onclick' => 'Text']));

    // input 태그 허용
    $def->addElement('input', 'Inline', 'Empty', 'Common', [
        'type' => 'Text',       // text, radio, checkbox 등
        'id' => 'Text',
        'name' => 'Text',
        'value' => 'Text',
        'checked' => 'Bool',
        'class' => 'Text',
    ]);

    // label 태그 허용
    $def->addElement('label', 'Inline', 'Flow', 'Common', [
        'for' => 'Text',
        'class' => 'Text',
    ]);
}, 1, 1);