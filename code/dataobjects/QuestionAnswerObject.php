<?php

/**
 * @author marcus
 */
class QuestionAnswerObject extends DataObject {
	private static $db = array(
		'Title'		=> 'Varchar(255)',
		'Content'	=> 'HTMLText',
		'Sort'		=> 'Int',
	);
	
	private static $has_one = array('Image' => 'Image');
	
	private static $belongs_many_many = array(
		'Pages'		=> 'QuestionAnswerPage',
	);
	
	private static $summary_fields = array(
		'Title',
		'Content',
	);
	
	private static $default_sort = 'Sort';
	
	public function canEdit($member = null) {
		$first = $this->Pages()->first();
		return $first ? $first->canEdit() : parent::canEdit($member);
	}
	
	public function canView($member = null) {
		$first = $this->Pages()->first();
		return $first ? $first->canView() : parent::canView($member);
	}
	
	public function canDelete($member = null) {
		$first = $this->Pages()->first();
		return $first ? $first->canDelete() : parent::canDelete($member);
	}
	
	public function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->removeByName('Sort');
		return $fields;
	}
	
	public function onAfterWrite() {
		parent::onAfterWrite();
		
		$pages = $this->Pages();
		foreach ($pages as $page) {
			$page->storeQuestionData();
		}
	}
}
