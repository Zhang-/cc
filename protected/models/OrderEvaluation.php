<?php

/**
 * This is the model class for table "order_evaluation".
 *
 * The followings are the available columns in table 'order_evaluation':
 * @property string $oid
 * @property integer $e_time
 * @property string $e_content
 * @property integer $e_level
 */
class OrderEvaluation extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return OrderEvaluation the static model class
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
		return 'order_evaluation';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('e_time, e_level', 'numerical', 'integerOnly'=>true),
			array('oid', 'length', 'max'=>19),
			array('e_content', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('oid, e_time, e_content, e_level', 'safe', 'on'=>'search'),
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
			'oid' => 'Oid',
			'e_time' => 'E Time',
			'e_content' => 'E Content',
			'e_level' => 'E Level',
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

		$criteria->compare('oid',$this->oid,true);
		$criteria->compare('e_time',$this->e_time);
		$criteria->compare('e_content',$this->e_content,true);
		$criteria->compare('e_level',$this->e_level);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}