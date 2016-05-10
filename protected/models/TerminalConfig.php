<?php

/**
 * This is the model class for table "terminal_config".
 *
 * The followings are the available columns in table 'terminal_config':
 * @property integer $id
 * @property string $tagname
 * @property string $tagvalue
 * @property string $tagdes
 * @property string $datatype
 * @property integer $isMend
 */
class TerminalConfig extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return TerminalConfig the static model class
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
		return 'terminal_config';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('isMend', 'numerical', 'integerOnly'=>true),
			array('tagname', 'length', 'max'=>50),
			array('tagvalue', 'length', 'max'=>255),
			array('datatype', 'length', 'max'=>10),
			array('tagdes', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, tagname, tagvalue, tagdes, datatype, isMend', 'safe', 'on'=>'search'),
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
			'tagname' => '配置项标签',
			'tagvalue' => '配置项值',
			'tagdes' => '配置项说明',
			'datatype' => '配置项类型',
			'isMend' => 'Is Mend',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('tagname',$this->tagname,true);
		$criteria->compare('tagvalue',$this->tagvalue,true);
		$criteria->compare('tagdes',$this->tagdes,true);
		$criteria->compare('datatype',$this->datatype,true);
		$criteria->compare('isMend',$this->isMend);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	/**
	 * 终端配置管理
	 */
	public function searchConfig(){
		$criteria=new CDbCriteria;

		if(!empty($this->tagname))
			$criteria->compare('tagname', $this->tagname, true);
		$criteria->addCondition('isMend = 1');

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}