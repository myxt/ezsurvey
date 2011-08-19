<div class="type-text">

	<label>{$question.question_number}.
	{$question.text|wash('xhtml')} {section show=$question.mandatory}<strong class="required">*</strong>{/section}</label>
	
	<div class="survey-choices">
	{section show=$question_result}
	  <input class="shortdate{if $question.mandatory} required{/if}" name="{$prefix_attribute}_ezsurvey_answer_{$question.id}_{$attribute_id}" type="text" value="{$question_result.text|wash('xhtml')}" />
	{section-else}
	  <input class="shortdate{if $question.mandatory} required{/if}" name="{$prefix_attribute}_ezsurvey_answer_{$question.id}_{$attribute_id}" type="text" value="{$question.answer|wash('xhtml')}" />
	{/section}
	</div>

</div>