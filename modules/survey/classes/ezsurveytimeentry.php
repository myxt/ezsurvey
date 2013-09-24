<?php

class eZSurveyTimeEntry extends eZSurveyEntry
{
    function __construct( $row = false )
    {
        if ( !isset( $row['mandatory'] ) )
            $row['mandatory'] = 1;
        $row['type'] = 'TimeEntry';
        $this->eZSurveyEntry( $row );
    }

    function processViewActions( &$validation, $params )
    {
        $http = eZHTTPTool::instance();
        $variableArray = array();
        
        $prefix = eZSurveyType::PREFIX_ATTRIBUTE;
        $attributeID = $params['contentobjectattribute_id'];

        $postAnswer = $prefix . '_ezsurvey_answer_' . $this->ID . '_' . $attributeID;
        $answer = trim ( $http->postVariable( $postAnswer ) );

        if ( $this->attribute( 'mandatory' ) == 1 && strlen( $answer ) == 0 )
        {
            $validation['error'] = true;
            $validation['errors'][] = array( 'message' => ezpi18n::tr( 'survey', 'Please answer the question %number as well!', null,
                                                                  array( '%number' => $this->questionNumber() ) ),
                                             'question_number' => $this->questionNumber(),
                                             'code' => 'time_answer_question',
                                             'question' => $this );
        }
        
        $data = explode( ':', $answer );
        $validator = new eZDateTimeValidator();
        if ( $this->attribute( 'mandatory' ) == 1 && $validator->validateTime( $data[0], $data[1] ) == eZInputValidator::STATE_INVALID )
        {
            $validation['error'] = true;
            $validation['errors'][] = array( 'message' => ezpi18n::tr( 'survey', 'Please enter a valid time!', null,
                                                                  array( '%number' => $this->questionNumber() ) ),
                                             'question_number' => $this->questionNumber(),
                                             'code' => 'time_answer_time',
                                             'question' => $this );
        	
        }

        $this->setAnswer( $answer );
        $variableArray['answer'] = $answer;
        
        return $variableArray;
    }
    
    function answer()
    {
        $http = eZHTTPTool::instance();

        if ( $this->Answer !== false )
            return $this->Answer;

        $prefix = eZSurveyType::PREFIX_ATTRIBUTE;

        $postSurveyAnswer = $prefix . '_ezsurvey_answer_' . $this->ID . '_' . $this->contentObjectAttributeID();
        if ( $http->hasPostVariable( $postSurveyAnswer ) && $http->postVariable( $postSurveyAnswer ) != '' )
        {
            $surveyAnswer = $http->postVariable( $postSurveyAnswer );
            return $surveyAnswer;
        }
        if ( $this->Default == 'now' && $http->postVariable( $postSurveyAnswer ) == '' )
    	{
    		return date('G:i');
    	}
        if ( $this->Default == 'empty' && $http->postVariable( $postSurveyAnswer ) == '' )
    	{
    		return '';
    	}
        return $this->Default;
    }
}

eZSurveyQuestion::registerQuestionType( 'Time Entry', 'TimeEntry' );

?>
