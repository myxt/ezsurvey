<label>{$question.question_number}.
{$question.text|wash('xhtml')} {section show=$question.mandatory}<strong class="required">*</strong>{/section}</label>

<div class="survey-choices">
{section show=$question_result}
  <input class="email" name="{$prefix_attribute}_ezsurvey_answer_{$question.id}_{$attribute_id}" type="text" size="20" value="{$question_result.text|wash('xhtml')}" />
{section-else}
  <input class="email" name="{$prefix_attribute}_ezsurvey_answer_{$question.id}_{$attribute_id}" type="text" size="20" value="{$question.answer|wash('xhtml')}" />
{/section}
</div>