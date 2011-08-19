<h2 class="attributetype">{"Time entry"|i18n('survey')}</h2>

<div class="block">
<label>{"Text of question"|i18n('survey')}:</label>
<input class="box" type="text" name="{$prefix_attribute}_ezsurvey_question_{$question.id}_text_{$attribute_id}" value="{$question.text|wash('xhtml')}" />
</div>

<div class="block">
<label>{"Code"|i18n('survey')}:</label>
<input type="text" name="{$prefix_attribute}_ezsurvey_question_{$question.id}_code_{$attribute_id}" value="{$question.code|wash('xhtml')}" />
</div>

<div class="block">
<input type="hidden" name="{$prefix_attribute}_ezsurvey_question_{$question.id}_mandatory_hidden_{$attribute_id}" value="1" />
<label><input type="checkbox" name="{$prefix_attribute}_ezsurvey_question_{$question.id}_mandatory_{$attribute_id}" value="1" {section show=$question.mandatory}checked{/section} />
{"Mandatory answer"|i18n('survey')}</label>
</div>

<div class="block">
<label>{"Default answer"|i18n('survey')}:</label>
<label><input type="radio" name="{$prefix_attribute}_ezsurvey_question_{$question.id}_default_{$attribute_id}" value="empty" {section show=$question.default_value|eq('empty')}checked{/section} />
{"Empty"|i18n('survey')}</label>
<label><input type="radio" name="{$prefix_attribute}_ezsurvey_question_{$question.id}_default_{$attribute_id}" value="now" {section show=$question.default_value|eq('now')}checked{/section} />
{"Now"|i18n('survey')}</label>
</div>
