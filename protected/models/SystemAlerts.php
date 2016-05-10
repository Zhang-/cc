<?php

/**
 * This is the model class for table "system_alerts".
 *
 * The followings are the available columns in table 'system_alerts':
 * @property integer $id
 * @property string $alerts_key
 * @property string $alerts_name
 * @property string $alerts_tag
 * @property string $alerts_link
 * @property string $alerts_status
 * @property integer $alerts_display
 * @property string $alerts_contents
 */
class SystemAlerts extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return SystemAlerts the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'system_alerts';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('alerts_key','unique'),
			array('alerts_key, alerts_name, alerts_tag, alerts_link, alerts_display', 'required'),
			array('alerts_display', 'numerical', 'integerOnly'=>true),
			array('alerts_key, alerts_status', 'length', 'max'=>20),
			array('alerts_name, alerts_tag', 'length', 'max'=>50),
			array('alerts_contents', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, alerts_key, alerts_name, alerts_tag, alerts_link, alerts_status, alerts_display, alerts_contents', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'alerts_key' => '关键词',
			'alerts_name' => '系统提醒名称',
			'alerts_tag' => '系统提醒标签',
			'alerts_link' => 'URL地址',
			'alerts_status' => '提醒状态',
			'alerts_display' => '显示状态',
			'alerts_contents' => '附加内容',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->addCondition("alerts_key like '%".$this->alerts_tag."%' or alerts_name like '%".$this->alerts_tag."%' or alerts_tag like '%".$this->alerts_tag."%'");

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}