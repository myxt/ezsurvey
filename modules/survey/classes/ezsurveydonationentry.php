<?php

class eZSurveyDonationEntry extends eZSurveyEntry
{
    function eZSurveyDonationEntry( $row = false )
    {
        if ( !isset( $row['mandatory'] ) )
            $row['mandatory'] = 1;
        $row['type'] = 'DonationEntry';
        $this->eZSurveyEntry( $row );
    }

    function processViewActions( &$validation, $params )
    {
        $http = eZHTTPTool::instance();
        $locale = eZLocale::instance();
        $variableArray = array();

        $prefix = eZSurveyType::PREFIX_ATTRIBUTE;
        $attributeID = $params['contentobjectattribute_id'];

        $postAnswer = $prefix . '_ezsurvey_answer_' . $this->ID . '_' . $attributeID;
        $answer = trim( $http->postVariable( $postAnswer ) );
        $variableArray['answer'] = trim ( $http->postVariable( $postAnswer ) );


        if ( $this->attribute( 'mandatory' ) == 1 && strlen( $answer ) == 0 )
        {
            $validation['error'] = true;
            $validation['errors'][] = array( 'message' => ezpI18n::tr( 'survey', 'Please answer the question %number as well!', null,
                                                                  array( '%number' => $this->questionNumber() ) ),
                                             'question_number' => $this->questionNumber(),
                                             'code' => 'donation_answer_question',
                                             'question' => $this );
            return $variableArray;
        }

        if ( strlen( $answer ) == 0 )
        {
            $this->setAnswer( '' );
            return $variableArray;
        }

        $answer = $locale->internalNumber( $answer );
        $min = $this->attribute( 'text2' );
        if ( strlen( $min ) == 0 )
            $min = false;
        $max = $this->attribute( 'text3' );
        if ( strlen( $max ) == 0 )
            $max = false;
        if ( $this->attribute( 'num' ) )
        {
            // due to bug in eZIntegerValidator: 6.00 is not integer for it...
            if ( is_numeric( $answer ) && (int) $answer == $answer )
                $answer = (int) $answer;
            $reqInteger = true;
            $validator = new eZIntegerValidator( $min, $max );
            if ( $min !== false )
                $minText = $min;
            if ( $max !== false )
                $maxText = $max;
        }
        else
        {
            $reqInteger = false;
            $validator = new eZFloatValidator( $min, $max );
            if ( $min !== false )
                $minText = $locale->formatNumber( $min );
            if ( $max !== false )
                $maxText = $locale->formatNumber( $max );
        }

        $this->setAnswer( $answer );

        switch ( $validator->validate( $answer ) )
        {
            case eZInputValidator::STATE_ACCEPTED:
            {
                $http = eZHTTPTool::instance();
                $http->setSessionVariable( "donation", $answer );
            }
            break;

            case eZInputValidator::STATE_INTERMEDIATE:
            {
                $validation['error'] = true;
                if ( $min == false && $max == false )
                {
                    if ( $reqInteger )
                    {
                        $validation['errors'][] = array( 'message' => ezpI18n::tr( 'survey', 'Entered text in the question %number is not an integer number!', null,
                                                                              array( '%number' => $this->questionNumber() ) ),
                                                         'question_number' => $this->questionNumber(),
                                                         'code' => 'donation_not_integer',
                                                         'question' => $this );
                    }
                    else
                    {
                        $validation['errors'][] = array( 'message' => ezpI18n::tr( 'survey', 'Entered text in the question %number is not a number!', null,
                                                                              array( '%number' => $this->questionNumber() ) ),
                                                         'question_number' => $this->questionNumber(),
                                                         'code' => 'donation_not_number',
                                                         'question' => $this );
                    }
                }
                else if ( $min == false )
                {
                    if ( $reqInteger )
                    {
                        $validation['errors'][] = array( 'message' => ezpI18n::tr( 'survey', 'Entered number in the question %number is not integer or is not lower than or equal to %max!', null,
                                                                              array( '%number' => $this->questionNumber(),
                                                                                     '%max' => $maxText ) ),
                                                         'question_number' => $this->questionNumber(),
                                                         'code' => 'donation_not_integer_or_lower_than_max',
                                                         'question' => $this );
                    }
                    else
                    {
                        $validation['errors'][] = array( 'message' => ezpI18n::tr( 'survey', 'Entered number in the question %number must be lower than or equal to %max!', null,
                                                                              array( '%number' => $this->questionNumber(),
                                                                                     '%max' => $maxText ) ),
                                                         'question_number' => $this->questionNumber(),
                                                         'code' => 'donation_lower_than_max',
                                                         'question' => $this );
                    }
                }
                else if ( $max == false )
                {
                    if ( $reqInteger )
                    {
                        $validation['errors'][] = array( 'message' => ezpI18n::tr( 'survey', 'Entered number in the question %number is not integer or is not greater than or equal to %min!', null,
                                                                              array( '%number' => $this->questionNumber(),
                                                                                     '%min' => $minText ) ),
                                                         'question_number' => $this->questionNumber(),
                                                         'code' => 'donation_not_integer_or_greater_to_min',
                                                         'question' => $this );
                    }
                    else
                    {
                        $validation['errors'][] = array( 'message' => ezpI18n::tr( 'survey', 'Entered number in the question %number must be greater than or equal to %min!', null,
                                                                              array( '%number' => $this->questionNumber(),
                                                                                     '%min' => $minText ) ),
                                                         'question_number' => $this->questionNumber(),
                                                         'code' => 'donation_greater_to_min',
                                                         'question' => $this );
                    }
                }
                else
                {
                    if ( $reqInteger )
                    {
                        $validation['errors'][] = array( 'message' => ezpI18n::tr( 'survey', 'Entered number in the question %number is not integer or is not between %min and %max!', null,
                                                                              array( '%number' => $this->questionNumber(),
                                                                                     '%min' => $minText,
                                                                                     '%max' => $maxText ) ),
                                                         'question_number' => $this->questionNumber(),
                                                         'code' => 'donation_not_integer_not_between_min_max',
                                                         'question' => $this );
                    }
                    else
                    {
                        $validation['errors'][] = array( 'message' => ezpI18n::tr( 'survey', 'Entered number in the question %number must be between %min and %max!', null,
                                                                              array( '%number' => $this->questionNumber(),
                                                                                     '%min' => $minText,
                                                                                     '%max' => $maxText ) ),
                                                         'question_number' => $this->questionNumber(),
                                                         'code' => 'donation_not_between_min_max',
                                                         'question' => $this );
                    }
                }
            } break;

            default:
            {
                $validation['error'] = true;
                if ( $reqInteger )
                {
                    $validation['errors'][] = array( 'message' => ezpI18n::tr( 'survey', 'Entered text in the question %number is not an integer number!', null,
                                                                          array( '%number' => $this->questionNumber() ) ),
                                                     'question_number' => $this->questionNumber(),
                                                     'code' => 'donation_not_integer',
                                                     'question' => $this );
                }
                else
                {
                    $validation['errors'][] = array( 'message' => ezpI18n::tr( 'survey', 'Entered text in the question %number is not a number!', null,
                                                                          array( '%number' => $this->questionNumber() ) ),
                                                     'question_number' => $this->questionNumber(),
                                                     'code' => 'donation_not_number',
                                                     'question' => $this );
                }
            }break;
        }

        return $variableArray;
    }

    function validateEditActions( &$validation, $params )
    {
        parent::validateEditActions( $validation, $params );

        $http = eZHTTPTool::instance();
        $locale = eZLocale::instance();
        $prefix = eZSurveyType::PREFIX_ATTRIBUTE;
        $attributeID = $params['contentobjectattribute_id'];

        $postNumHidden = $prefix . '_ezsurvey_question_' . $this->ID . '_num_hidden_' . $attributeID;
        $postNum = false;
        if ( $http->hasPostVariable( $postNumHidden ) )
        {
            $postNum = $prefix . '_ezsurvey_question_' . $this->ID . '_num_' . $attributeID;
        }

        if ( $postNum !== false and $http->hasPostVariable( $postNum ) )
        {
            $reqInteger = true;
            $validator = new eZIntegerValidator();
        }
        else
        {
            $reqInteger = false;
            $validator = new eZFloatValidator();
        }

        $postNumText2 = $prefix . '_ezsurvey_question_' . $this->ID . '_text2_' . $attributeID;
        if ( $http->hasPostVariable( $postNumText2 ) and
             strlen( trim( $http->postVariable( $postNumText2 ) ) ) > 0 )
        {
            $data = $locale->internalNumber( trim( $http->postVariable( $postNumText2 ) ) );
            if ( $reqInteger and is_numeric( $data ) and (int) $data == $data )
                $data = (int) $data;
            if ( $validator->validate( $data ) != eZInputValidator::STATE_ACCEPTED )
            {
                $validation['error'] = true;
                if ( $reqInteger )
                {
                    $validation['errors'][] = array( 'message' => ezpI18n::tr( 'survey', 'Entered text in the question with id %number is not an integer number!', null,
                                                                          array( '%number' => $this->ID ) ),
                                                     'question_id' => $this->ID,
                                                     'code' => 'donation_not_integer_number',
                                                     'question' => $this );
                }
                else
                {
                    $validation['errors'][] = array( 'message' => ezpI18n::tr( 'survey', 'Entered text in the question with id %number is not an number!', null,
                                                                          array( '%number' => $this->ID ) ),
                                                     'question_id' => $this->ID,
                                                     'code' => 'donation_not_number',
                                                     'question' => $this );
                }
            }
        }

        $postNumText3 = $prefix . '_ezsurvey_question_' . $this->ID . '_text3_' . $attributeID;
        if ( $http->hasPostVariable( $postNumText3 ) and
             strlen( $http->postVariable( $postNumText3 ) ) > 0 )
        {
            $data = $locale->internalNumber( trim( $http->postVariable( $postNumText3 ) ) );
            if ( $reqInteger and is_numeric( $data ) and (int) $data == $data )
                $data = (int) $data;
            if ( $validator->validate( $data ) != eZInputValidator::STATE_ACCEPTED )
            {
                $validation['error'] = true;
                if ( $reqInteger )
                {
                    $validation['errors'][] = array( 'message' => ezpI18n::tr( 'survey', 'Entered text in the question with id %number is not an integer number!', null,
                                                                          array( '%number' => $this->ID ) ),
                                                     'question_id' => $this->ID,
                                                     'code' => 'donation_not_integer_number',
                                                     'question' => $this );
                }
                else
                {
                    $validation['errors'][] = array( 'message' =>  ezpI18n::tr( 'survey', 'Entered text in the question with id %number is not an number!', null,
                                                                           array( '%number' => $this->ID ) ),
                                                     'question_id' => $this->ID,
                                                     'code' => 'donation_not_number',
                                                     'question' => $this );
                }
            }
        }

        $postNumDefaultValue = $prefix . '_ezsurvey_question_' . $this->ID . '_default_value_' . $attributeID;
        if ( $http->hasPostVariable( $postNumDefaultValue ) and
             strlen( $http->postVariable( $postNumDefaultValue ) ) > 0 )
        {
            $data = $locale->internalNumber( trim( $http->postVariable( $postNumDefaultValue ) ) );

            if ( $reqInteger && is_numeric( $data ) && (int) $data == $data )
                $data = (int) $data;
            if ( $validator->validate( $data ) != eZInputValidator::STATE_ACCEPTED )
            {
                $validation['error'] = true;
                if ( $reqInteger )
                {
                    $validation['errors'][] = array( 'message' => ezpI18n::tr( 'survey', 'Entered text in the question with id %number is not an integer number!', null,
                                                                          array( '%number' => $this->ID ) ),
                                                     'question_id' => $this->ID,
                                                     'code' => 'donation_not_integer_number',
                                                     'question' => $this );
                }
                else
                {
                    $validation['errors'][] = array( 'message' => ezpI18n::tr( 'survey', 'Entered text in the question with id %number is not an number!', null,
                                                                          array( '%number' => $this->ID ) ),
                                                     'question_id' => $this->ID,
                                                     'code' => 'donation_not_number',
                                                     'question' => $this );
                }
            }
        }

    }

    function processEditActions( &$validation, $params )
    {
        parent::processEditActions( $validation, $params );
        $http = eZHTTPTool::instance();
        $locale = eZLocale::instance();
        $prefix = eZSurveyType::PREFIX_ATTRIBUTE;
        $attributeID = $params['contentobjectattribute_id'];
        $postNumHidden = $prefix . '_ezsurvey_question_' . $this->ID . '_num_hidden_' . $attributeID;
        if ( $http->hasPostVariable( $postNumHidden ) )
        {
            $postNum = $prefix . '_ezsurvey_question_' . $this->ID . '_num_' . $attributeID;
            if ( $http->hasPostVariable( $postNum ) )
                $newNum = 1;
            else
                $newNum = 0;
            if ( $this->attribute( 'num' ) != $newNum )
                $this->setAttribute( 'num', $newNum );
        }

        if ( $this->attribute( 'num' ) )
        {
            $reqInteger = true;
            $validator = new eZIntegerValidator();
        }
        else
        {
            $reqInteger = false;
            $validator = new eZFloatValidator();
        }

        $this->setAttribute( 'text2', trim( $this->attribute( 'text2' ) ) );
        $this->setAttribute( 'text3', trim( $this->attribute( 'text3' ) ) );
        $this->setAttribute( 'default_value', trim( $this->attribute( 'default_value' ) ) );

        if ( strlen( $this->attribute( 'text2' ) ) > 0 )
        {
            $data = $this->attribute( 'text2' );
            $data = trim( $data );
            $data = $locale->internalNumber( $data );
            if ( $reqInteger && is_numeric( $data ) and (int) $data == $data )
                $data = (int) $data;
            if ( $validator->validate( $data ) == eZInputValidator::STATE_ACCEPTED )
            {
                $this->setAttribute( 'text2', $data );
            }
        }

        if ( strlen( $this->attribute( 'text3' ) ) > 0 )
        {
            $data = $this->attribute( 'text3' );
            $data = trim( $data );
            $data = $locale->internalNumber( $data );
            if ( $reqInteger && is_numeric( $data ) && (int) $data == $data )
                $data = (int) $data;
            if ( $validator->validate( $data ) == eZInputValidator::STATE_ACCEPTED )
            {
                $this->setAttribute( 'text3', $data );
            }
        }

        if ( strlen( $this->attribute( 'default_value' ) ) > 0 )
        {
            $data = $this->attribute( 'default_value' );
            $data = trim( $data );
            $data = $locale->internalNumber( $data );
            if ( $reqInteger && is_numeric( $data ) && (int) $data == $data )
                $data = (int) $data;
            if ( $validator->validate( $data ) == eZInputValidator::STATE_ACCEPTED )
            {
                $this->setAttribute( 'default_value', $data );
            }
        }
    }
}

eZSurveyQuestion::registerQuestionType( ezpI18n::tr( 'survey', 'Donation Entry' ), 'DonationEntry' );

?>
