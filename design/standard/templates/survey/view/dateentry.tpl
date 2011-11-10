<div class="type-text">

    <label>{$question.question_number}.
    {$question.text|wash('xhtml')} {section show=$question.mandatory}<strong class="required">*</strong>{/section}</label>

    <div class="survey-choices">
    {section show=$question_result}
      <input class="shortdate{if $question.mandatory} required{/if}" name="{$prefix_attribute}_ezsurvey_answer_{$question.id}_{$attribute_id}" type="text" value="{if $question_result}{$question_result.text|wash('xhtml')}{else}DD-MM-JJJJ{/if}" />
    {section-else}
      <input class="shortdate{if $question.mandatory} required{/if}" name="{$prefix_attribute}_ezsurvey_answer_{$question.id}_{$attribute_id}" type="text" value="{if $question.answer}{$question.answer|wash('xhtml')}{else}DD-MM-JJJJ{/if}" />
    {/section}
    </div>

</div>
