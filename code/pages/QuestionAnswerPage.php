<?php

/**
 * @author marcus
 */
class QuestionAnswerPage extends Page
{
    private static $db = array(
        'StoredQuestions'        => 'MultiValueField',
    );
    
    private static $many_many = array(
        'Questions'                => 'QuestionAnswerObject'
    );
    
    private static $defaults = array(
        'Content'        => '$Items',
    );
    
    
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        
        $contentMsg = 'Insert the $Items keyword to display the item list';
        $fields->addFieldToTab('Root.Main', LiteralField::create('ContentMsg', "<strong>$contentMsg</strong>"), 'Content');
        
        $config = GridFieldConfig::create()
            ->addComponent(new GridFieldButtonRow('before'))
            ->addComponent(new GridFieldAddNewButton('buttons-before-left'))
            ->addComponent(new GridFieldDetailForm())
            ->addComponent(new GridFieldToolbarHeader())
            ->addComponent(new GridFieldTitleHeader())
            ->addComponent($cols = new GridFieldEditableColumns())
            ->addComponent(new GridFieldOrderableRows())
            ->addComponent(new GridFieldEditButton())
            ->addComponent(new GridFieldDeleteAction());
        
        
        $display = array(
            'Title'    => true,
            'Content'        => function ($record, $col, $grid) {
                return HtmlEditorField::create('Content', 'Content')->setRows(8);
            },
        );
        
        $cols->setDisplayFields($display);
        $gf = GridField::create('Questions', 'Questions', $this->Questions(), $config);
        $fields->addFieldToTab('Root.Questions', $gf);
        
        $msg = 'Please note that changes to the questions here will not appear on the published site until the page is published';
        $notice = LiteralField::create('FaqNotice', "<strong>$msg</strong>");
        $fields->addFieldToTab('Root.Questions', $notice, 'Questions');
        
        return $fields;
    }
    
    public function onBeforeWrite()
    {
        parent::onBeforeWrite();
        
        $this->storeQuestionData(true);
    }
    
    public function storeQuestionData($noWrite = false)
    {
        if ($this->ID) {
            $this->StoredQuestions = $this->Questions()->map('Title', 'Content')->toArray();
            
            $this->extend('updateStoredQuestions');
            
            if (!$noWrite) {
                $this->write();
            }
        }
    }
}

class QuestionAnswerPage_Controller extends Page_Controller
{
    public function index()
    {
        if ($this->Content) {
            $hasLocation = stristr($this->Content, '$Items');
            if ($hasLocation) {
                $faqlayout = $this->renderWith('QuestionListing');
                $content = str_ireplace('$Items', $faqlayout, $this->Content);
                return array(
                    'Content' => DBField::create_field('HTMLText', $content),
                );
            }
        }

        return array(
            'Content' => DBField::create_field('HTMLText', $this->Content),
        );
    }
}
