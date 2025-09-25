@noindent
<codeBook ID="{{$a('f_1_1')}}" xml:lang="en" version="2.5"
          xsi:schemaLocation="ddi:codebook:2_5 http://www.ddialliance.org/Specification/DDI-Codebook/2.5/XMLSchema/codebook.xsd"
          xmlns="ddi:codebook:2_5" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/"
          xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <docDscr>
        <citation>
            <titlStmt>
                <titl>{{$a('f_1_3')}}</titl>
            </titlStmt>
            <dcterms:spatial>{{$a('f_1_0')}}</dcterms:spatial>
            <dc:identifier>{{$survey->id}}</dc:identifier>
            @valid($a('f_11_1', true))
            @foreach($a('f_11_1') as $contributor)
                <dc:contributor>{{$contributor}}</dc:contributor>
            @endforeach
            @endvalid

            @valid($a('f_11_2', true))
            <dcterms:modified>{{$a('f_11_2')}}</dcterms:modified>
            @endvalid

            @valid($a('f_11_3', true))
            <dcterms:temporal>{{$a('f_11_3')}}</dcterms:temporal>
            @endvalid

            @valid($a('f_11_4', true))
            <dcterms:temporal>{{$a('f_11_4')}}</dcterms:temporal>
            @endvalid

            @valid($a('f_11_5', true))
            <dc:coverage>{{$a('f_11_5')}}</dc:coverage>
            @endvalid

            @valid($a('f_11_6', true))
            <dc:coverage>{{$a('f_11_6')}}</dc:coverage>
            @endvalid

            @valid($a('f_11_7', true))
            <dc:coverage>{{$a('f_11_7')}}</dc:coverage>
            @endvalid
        </citation>
    </docDscr>
    <stdyDscr>
        <citation>
            <titlStmt>
                @valid($a('f_1_4', true))
                <titl>{{$a('f_1_4')}}</titl>
                @endvalid

                @valid($a('f_1_2', true))
                <altTitl>{{$a('f_1_2')}}</altTitl>
                @endvalid

                <parTitl xml:lang="en">{{$a('f_1_3')}}</parTitl>
                <IDNo>{{$a('f_1_1')}}</IDNo>
            </titlStmt>
            <prodStmt>
                @valid($a('f_9_1', true))
                <producer>{{$a('f_9_1')}}</producer>
                @endvalid

                @valid($a('f_9_2', true))
                <copyright>{{$a('f_9_2')}}</copyright>
                @endvalid
            </prodStmt>
            @valid($a('f_9_3', true))
            <distStmt>
                <distrbtr>{{$a('f_9_3')}}</distrbtr>
            </distStmt>
            @endvalid
            @valid($a('f_2_6', true))
            <serStmt>
                <serInfo>{{$a('f_2_6')}}</serInfo>
            </serStmt>
            @endvalid
            @valid($a('f_9_5', true))
            <biblCit>{{$a('f_9_5')}}</biblCit>
            @endvalid
            @valid($a('f_9_8', true))
            <notes>{{$a('f_9_8')}}</notes>
            @endvalid
        </citation>
        <stdyInfo>
            <subject>
            @noindent
            @foreach($fields->whereRegex('field_code', '/^f_1_13_\d+$/') as $field)
                @if($a($field->field_code) === 'Yes')
                    <keyword>{{$field->name}}</keyword>
                @endif
            @endforeach
            @valid($a('f_1_13_30a', true))
                @foreach($a('f_1_13_30a') as $keyword)
                    @valid($keyword)
                    <keyword>{{$keyword}}</keyword>
                    @endvalid
                @endforeach
            @endvalid
            @endnoindent
            </subject>
            <sumDscr>
                <collDate event="start">{{$a('f_1_11')}}</collDate>
                <collDate event="end">{{$a('f_1_12')}}</collDate>
                @valid($a('f_2_4', true))
                <collDate cycle="{{$a('f_2_4')}}"></collDate>
                @endvalid
                <nation abbr="{{$a('f_1_0', true)}}">{{$a('f_1_0')}}</nation>
                <geogCover>
                    <![CDATA[
                    @valid($a('f_1_5', true))
                    {{$a('f_1_5')}}
                    @endvalid
                    @valid($a('f_1_6', true))
                    @foreach($a('f_1_6') as $item)
                        @valid($item)
                        {{$item}}
                        @endvalid
                    @endforeach
                    @endvalid
                    @valid($a('f_1_7', true))
                    @foreach($a('f_1_7') as $item)
                        @valid($item)
                        {{$item}}
                        @endvalid
                    @endforeach
                    @endvalid
                    @valid($a('f_1_8', true))
                    @foreach($a('f_1_8') as $item)
                        @valid($item)
                        {{$item}}
                        @endvalid
                    @endforeach
                    @endvalid
                    @valid($a('f_1_8a', true))
                    @foreach($a('f_1_8a') as $item)
                        @valid($item)
                        {{$item}}
                        @endvalid
                    @endforeach
                    @endvalid
                    @valid($a('f_1_8b', true))
                    {{$a('f_1_8b')}}
                    @endvalid
                    ]]>
                </geogCover>
                @valid([$a('f_1_15', true), $a('f_1_16', true)])
                <universe clusion="I">
                    <![CDATA[
                    @valid($a('f_1_15', true))
                    {{$a('f_1_15')}}
                    @endvalid
                    @valid($a('f_1_16', true))
                    {{$a('f_1_16')}}
                    @endvalid
                    ]]>
                </universe>
                @endvalid
            </sumDscr>
            @valid([$a('f_2_9', true), $a('f_2_10', true)])
            <notes subject="subsurvey">
                @valid($a('f_2_9', true))
                {{$a('f_2_9')}},
                @endvalid
                @valid($a('f_2_10', true))
                {{$a('f_2_10')}}
                @endvalid
            </notes>
            @endvalid
            @valid($a('f_2_15', true))
            <notes subject="inclusion in a large survey">
                {{$a('f_2_15')}}
            </notes>
            @endvalid
        </stdyInfo>
        <method>
            <dataColl>
                <timeMeth method="{{$a('f_1_10')}}"></timeMeth>
                @valid($a('f_7_1', true))
                <dataCollector>{{$a('f_7_1')}}</dataCollector>
                @endvalid
                @valid($a('f_2_7', true))
                <frequenc>{{$a('f_2_7')}}</frequenc>
                @endvalid
                <sampProc>
                    <![CDATA[
                    @valid([$a('f_2_11', true),$a('f_2_12', true),$a('f_2_12a', true)])
                    Pooled sample: @valid($a('f_2_11', true)){{$a('f_2_11')}},@endvalid @valid($a('f_2_12', true)){{$a('f_2_12')}},@endvalid @valid($a('f_2_12a', true)){{$a('f_2_12a')}}@endvalid
                    @endvalid
                    @valid($a('f_3_6', true))

                    Survey include subgroup of majority pop: {{$a('f_3_6')}}
                    @endvalid
                    @valid($a('f_3_6a', true))
                    Survey designed as a general population survey: {{$a('f_3_6a')}}
                    @endvalid
                    @valid($a('f_4_1', true))
                    Sampling strategy - closed: {{$a('f_4_1')}}
                    @endvalid
                    @valid($a('f_4_2', true))
                    Sampling strategy - open: {{$a('f_4_2')}}
                    @endvalid
                    ]]>
                </sampProc>
                <sampleFrame>
                    <txt>
                        <![CDATA[
                        Representative of the population: {{$a('f_1_9')}}
                        @valid($a('f_4_3', true))
                        Sample design - full information: {{$a('f_4_3')}}
                        @endvalid
                        ]]>
                    </txt>
                    <universe clusion="I">
                        @noindent
                        <![CDATA[
                        @valid($a('f_3_1', true))
                        EMM Target population: which minority group(s): {{$a('f_3_1')}}
                        @endvalid
                        @valid($a('f_3_2', true))
                        Was the EMM target population…: {{$a('f_3_2')}}@valid($a('f_3_2a', true)); {{$a('f_3_2a')}} @endvalid
                        @endvalid

                        @spaceless
                        Operationalization of target population:&nbsp;
                        @foreach($fields->whereRegex('field_code', '/^f_3_3_\d+$/') as $field)
                            @if(is_array($a($field->field_code, true)))
                                {{implode($a($field->field_code,true),';&nbsp;')}}
                                ;&nbsp;
                            @elseif($a($field->field_code) === 'Yes')
                                {{$field->name}}
                                @unless($loop->last) ;&nbsp; @endunless
                            @endif
                        @endforeach
                        @endspaceless

                        @valid($a('f_4_4', true))
                        Sampling frame(s): {{$a('f_4_4')}}
                        @endvalid
                        ]]>
                        @endnoindent
                    </universe>
                    <frameUnit ID="frameUnit_total">
                        @valid([$a('f_3_5'), $a('f_4_5')])
                        <unitType numberOfUnits="00000">
                            <![CDATA[
                            @valid($a('f_3_5'))
                            Size of the EMM target pop. as a whole: {{$a('f_3_5')}}
                            @endvalid
                            @valid($a('f_4_5'))
                            Sampling units: {{$a('f_4_5')}}
                            @endvalid
                            ]]>
                        </unitType>
                        @endvalid
                        <txt>[Total]</txt>
                    </frameUnit>
                    @valid($a('f_6a1', true))
                    <frameUnit ID="frameUnit_sg1">
                        <unitType></unitType>
                        <txt>{{$a('f_6a1')}}</txt>
                    </frameUnit>
                    @endvalid
                    @valid($a('f_6b1', true))
                    <frameUnit ID="frameUnit_sg2">
                        <unitType></unitType>
                        <txt>{{$a('f_6b1')}}</txt>
                    </frameUnit>
                    @endvalid
                </sampleFrame>
                @valid($a('f_5_1'))
                <targetSampleSize>
                    <sampleSize ID="sampleSize_total">{{$a('f_5_1')}}</sampleSize>
                </targetSampleSize>
                @endvalid
                @valid($a('f_6a2'))
                <targetSampleSize>
                    <sampleSize ID="sampleSize_sg1">{{$a('f_6a2')}}</sampleSize>
                </targetSampleSize>
                @endvalid
                @valid($a('f_6b2'))
                <targetSampleSize>
                    <sampleSize ID="sampleSize_sg2">{{$a('f_6b2')}}</sampleSize>
                </targetSampleSize>
                @endvalid
                <collMode>
                    @spaceless
                    @foreach($fields->whereRegex('field_code', '/^f_7_2_\d+$/') as $field)
                        @if($a($field->field_code) === 'Yes')
                            {{$field->name}}
                            @unless($loop->last) ;&nbsp; @endunless
                        @endif
                    @endforeach
                    @endspaceless

                </collMode>
                @valid($a('f_10_3', true))
                <sources>
                    <srcDocu>{{$a('f_10_3')}}</srcDocu>
                </sources>
                @endvalid
                <collSitu>
                    <![CDATA[
                    @valid($a('f_7_3', true))
                    Who interviewed: {{$a('f_7_3')}}
                    @endvalid
                    @valid($a('f_7_4', true))
                    Interviewers spoke migrant languages: {{$a('f_7_4')}} @valid($a('f_7_5')), {{$a('f_7_5')}}@endvalid
                    @endvalid
                    @valid($a('f_7_6', true))
                    Questionnaire in migrant language: {{$a('f_7_6')}}
                    @endvalid
                    @valid($a('f_7_7', true))
                    Language of questionnaire: {{$a('f_7_7')}}
                    @endvalid
                    @valid($a('f_7_8', true))
                    Average duration/length of interview: {{$a('f_7_8')}}
                    @endvalid
                    @valid($a('f_7_9', true))
                    Number of questions: {{$a('f_7_9')}}
                    @endvalid
                    ]]>
                </collSitu>
                @valid($a('f_5_7', true))
                <weight>
                    <![CDATA[
                    Are weights provided: {{$a('f_5_7')}}
                    @valid($a('f_5_8', true))
                    Description: {{$a('f_5_8')}}
                    @endvalid
                    ]]>
                </weight>
                @endvalid
            </dataColl>
            @valid($a('f_4_6'))
            <notes subject="sampling method">{{$a('f_4_6')}}</notes>
            @endvalid
            @valid($a('f_5_9'))
            <notes subject="sample size">{{$a('f_5_9')}}</notes>
            @endvalid
            @valid($a('f_6a7'))
            <notes subject="SG1 issues">{{$a('f_6a7')}}</notes>
            @endvalid
            @valid($a('f_6b7'))
            <notes subject="SG2 issues">{{$a('f_6b7')}}</notes>
            @endvalid
            @valid($a('f_7_10'))
            <notes subject="data collection">{{$a('f_7_10')}}</notes>
            @endvalid
            <anlyInfo>
                <respRate ID="respRate_total">
                    <![CDATA[
                    @valid($a('f_5_2', true))
                    Total net/achieved sample: {{$a('f_5_2')}}
                    @endvalid
                    @valid($a('f_5_3', true))
                    Overall response rate: {{$a('f_5_3')}}
                    @endvalid
                    @valid($a('f_5_4', true))
                    Overall response rate calculated: {{$a('f_5_4')}} @valid($a('f_5_5', true))
                    , {{$a('f_5_5')}} @endvalid
                    @endvalid
                    ]]>
                </respRate>
                <respRate ID="respRate_sg1">
                    <![CDATA[
                    @valid($a('f_6a3', true))
                    SG1 net/achieved sample: {{$a('f_6a3')}}
                    @endvalid
                    @valid($a('f_6a4', true))
                    SG1 Response rate: {{$a('f_6a4')}}
                    @endvalid
                    @valid($a('f_6a5', true))
                    SG1 Overall response rate calculated: {{$a('f_6a5')}} @valid($a('f_6a6', true)), {{$a('f_6a6')}} @endvalid
                    @endvalid
                    ]]>
                </respRate>
                <respRate ID="respRate_sg2">
                    <![CDATA[
                    @valid($a('f_6b3', true))
                    SG2 net/achieved sample: {{$a('f_6b3')}}
                    @endvalid
                    @valid($a('f_6b4', true))
                    SG2 Response rate: {{$a('f_6b4')}}
                    @endvalid
                    @valid($a('f_6b5', true))
                    SG2 Overall response rate calculated: {{$a('f_6b5')}} @valid($a('f_6b6', true)), {{$a('f_6b6')}} @endvalid
                    @endvalid
                    ]]>
                </respRate>
                @valid($a('f_5_6', true))
                <dataAppr ID="dataAppr_total">{{$a('f_5_6')}}</dataAppr>
                @endvalid
                @valid($a('f_6a7', true))
                <dataAppr ID="dataAppr_sg1">{{$a('f_6a7')}}</dataAppr>
                @endvalid
            </anlyInfo>
            <stdyClas>Survey in development/not yet completed: {{$a('f_1_12a')}}</stdyClas>
        </method>
        <dataAccs>
            <setAvail>
                @valid($a('f_8_2', true))
                <accsPlac>{{$a('f_8_2')}}</accsPlac>
                @endvalid
                @valid([$a('f_8_1', true),$a('f_8_5', true),$a('f_8_6', true),$a('f_8_7', true)])
                <avlStatus>
                    <![CDATA[
                    @valid($a('f_8_1'))
                    Availability of the survey dataset: {{$a('f_8_1')}}
                    @endvalid
                    @valid($a('f_8_5'))
                    Access to complete dataset: {{$a('f_8_5')}}
                    @endvalid
                    @valid($a('f_8_6'))
                    Access to portions of dataset: {{$a('f_8_6')}}
                    @endvalid
                    @valid($a('f_8_7'))
                    Access to aggregate data results: {{$a('f_8_7')}}
                    @endvalid
                    ]]>
                </avlStatus>
                @endvalid
            </setAvail>
            @valid($a('f_8_8', true))
            <useStmt>
                <restrctn>{{$a('f_8_8')}}</restrctn>
            </useStmt>
            @endvalid
            @valid($a('f_8_21', true))
            <notes>{{$a('f_8_21')}}</notes>
            @endvalid
        </dataAccs>
        <othrStdyMat>
            <relMat ID="relMat_technical">
                <citation>
                    <titlStmt>
                        <titl>Technical survey documentation</titl>
                        @valid($a('f_8_12'))
                        <IDNo>{{$a('f_8_12')}}</IDNo>
                        @endvalid
                    </titlStmt>
                    @valid($a('f_8_13'))
                    <verStmt>
                        <version>{{$a('f_8_13')}}</version>
                    </verStmt>
                    @endvalid

                    @valid($a('f_9_6'))
                    <biblCit>{{$a('f_9_6')}}</biblCit>
                    @endvalid
                    @valid($a('f_8_11'))
                    <holdings URI="{{$a('f_8_11')}}"></holdings>
                    @endvalid
                    @valid($a('f_8_10'))
                    <dcterms:available>{{$a('f_8_10')}}</dcterms:available>
                    @endvalid
                    @valid($a('f_8_14'))
                    <dcterms:conformsTo>{{$a('f_8_14')}}</dcterms:conformsTo>
                    @endvalid
                    @valid($a('f_8_15'))
                    <dc:language>{{$a('f_8_15')}}</dc:language>
                    @endvalid
                    @valid($a('f_8_15a'))
                    @foreach($a('f_8_15a') as $lang)
                        @valid($lang)
                        <dc:language>{{$lang}}</dc:language>
                        @endvalid
                    @endforeach
                    @endvalid
                </citation>
            </relMat>
            <relMat ID="relMat_questionnaire">
                <citation>
                    <titlStmt>
                        <titl>Questionnaire</titl>
                        @valid($a('f_8_18'))
                        <IDNo>{{$a('f_8_18')}}</IDNo>
                        @endvalid
                    </titlStmt>
                    @valid($a('f_8_18'))
                    <verStmt>
                        <version>{{$a('f_8_19')}}</version>
                    </verStmt>
                    @endvalid
                    @valid($a('f_8_17'))
                    <holdings URI="{{$a('f_8_17')}}"></holdings>
                    @endvalid
                    @valid($a('f_8_16'))
                    <dcterms:available>{{$a('f_8_16')}}</dcterms:available>
                    @endvalid
                    @valid($a('f_8_20'))
                    <dc:language>{{$a('f_8_20')}}</dc:language>
                    @endvalid
                    @valid($a('f_8_20a'))
                    @foreach($a('f_8_20a') as $lang)
                        @valid($lang)
                        <dc:language>{{$lang}}</dc:language>
                        @endvalid
                    @endforeach
                    @endvalid
                </citation>
            </relMat>
            <relStdy>
                <citation>
                    <titlStmt>
                        @valid($a('f_2_4'))
                        <titl>{{$a('f_2_4')}}</titl>
                        @else
                            <titl>{{$a('f_2_3')}}</titl>
                            @endvalid
                            @valid($a('f_2_2'))
                            <altTitl>{{$a('f_2_2')}}</altTitl>
                            @endvalid
                            @valid($a('f_2_3'))
                            <parTitl xml:lang="en">{{$a('f_2_3')}}</parTitl>
                            @endvalid
                            @valid($a('f_2_1'))
                            <IDNo>{{$a('f_2_1')}}</IDNo>
                            @endvalid
                    </titlStmt>
                    @valid($a('f_2_5'))
                    <dcterms:spatial>{{$a('f_2_5')}}</dcterms:spatial>
                    @endvalid
                </citation>
            </relStdy>
            @valid([$a('f_2_13'), $a('f_2_14')])
            <relStdy>
                <citation>
                    @valid($a('f_2_13'))
                    <titlStmt>
                        <titl>{{$a('f_2_13')}}</titl>
                    </titlStmt>
                    @endvalid
                    @valid($a('f_2_14'))
                    <notes>{{$a('f_2_14')}}</notes>
                    @endvalid
                </citation>
            </relStdy>
            @endvalid
            <othRefs>
                <citation>
                    <titlStmt>
                        <titl>Any other publications</titl>
                    </titlStmt>
                    @valid($a('f_9_7'))
                    <biblCit>{{$a('f_9_7')}}</biblCit>
                    @endvalid
                </citation>
            </othRefs>
        </othrStdyMat>
        @valid($a('f_1_14a'))
        <notes subject="main purpose">{{ implode('; ', $a('f_1_14a'))}}</notes>
        @endvalid
        @valid($a('f_1_17'))
        <notes subject="general identification information">{{$a('f_1_17')}}</notes>
        @endvalid
        @valid($a('f_10_4'))
        <notes subject="any other comments">{{$a('f_10_4')}}</notes>
        @endvalid
    </stdyDscr>
    <fileDscr>
        <fileTxt>
            @valid([$a('f_8_3'),$a('f_8_4')])
            <fileCitation>
                @valid($a('f_8_3'))
                <titlStmt>
                    <titl></titl>
                    <IDNo>{{$a('f_8_3')}}</IDNo>
                </titlStmt>
                @endvalid
                @valid($a('f_8_4'))
                <verStmt>
                    <version>{{$a('f_8_4')}}</version>
                </verStmt>
                @endvalid
            </fileCitation>
            @endvalid
@noindent
            <fileCont>
                <![CDATA[
                @spaceless
                Migrant/minority related questions:&nbsp;
                @foreach($fields->whereRegex('field_code', '/^f_3_4_\d+$/') as $field)
                    @if($a($field->field_code) === 'Yes')
                        {{$field->name}}
                        @unless($loop->last) ;&nbsp; @endunless
                    @endif
                @endforeach
                @endspaceless
                @valid($a('f_8_9'))

                    @spaceless
                    Dataset language(s) available:&nbsp;
                    @valid($a('f_8_9'))
                        {{$a('f_8_9')}}
                    @endvalid
                    @valid($a('f_8_9a'))
                        ;&nbsp;
                        @foreach($a('f_8_9a') as $item)
                            {{$item}}
                            @unless($loop->last); @endunless
                        @endforeach
                    @endvalid
                    @endspaceless
                @endvalid

                ]]>
            </fileCont>
@endnoindent
        </fileTxt>
        @valid($a('f_3_7'))
        <notes>{{$a('f_3_7')}}</notes>
        @endvalid
    </fileDscr>
</codeBook>
@endnoindent
